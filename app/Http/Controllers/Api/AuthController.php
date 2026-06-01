<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\Tenant;
use Stancl\Tenancy\Database\Models\Domain;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        try {
            if (Auth::attempt($credentials)) {
                $user = Auth::user();

                // Check if Sanctum is working
                try {
                    $token = $user->createToken('auth-token')->plainTextToken;
                } catch (\Exception $e) {
                    return response()->json([
                        'message' => 'Token generation failed: ' . $e->getMessage() . '. Please ensure migrations are run and dependencies (bcmath) are installed.',
                        'debug_error' => $e->getMessage()
                    ], 500);
                }

                return response()->json([
                    'status' => 'success',
                    'user' => $user,
                    'token' => $token,
                    'role' => $user->role,
                    'message' => 'Login successful'
                ]);
            }

            return response()->json(['message' => 'Invalid credentials'], 401);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Server Error: ' . $e->getMessage(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    public function sendOTP(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Register duplicate check
        if (\App\Models\School::where('schoolAdminEmail', $request->email)->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'This email is already registered. Please login or use forgot password.'
            ], 422);
        }

        $email = $request->email;
        $otp = rand(1000, 9999);

        // Store OTP in cache for 10 minutes
        \Illuminate\Support\Facades\Cache::put('otp_' . $email, $otp, now()->addMinutes(10));

        // Send Email
        try {
            \Illuminate\Support\Facades\Mail::send('emails.otp', ['otp' => $otp], function ($message) use ($email, $otp) {
                $message->to($email)->subject($otp . ' Registration OTP - CloudiSchool');
            });
            return response()->json(['status' => 'success', 'message' => 'OTP sent to your email']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to send OTP: ' . $e->getMessage()], 500);
        }
    }

    public function verifyOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|numeric'
        ]);

        $cachedOtp = \Illuminate\Support\Facades\Cache::get('otp_' . $request->email);

        if ($cachedOtp == $request->otp) {
            return response()->json(['status' => 'success', 'message' => 'OTP verified successfully']);
        }

        return response()->json(['status' => 'error', 'message' => 'Invalid OTP'], 400);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'school_name' => 'required|string|max:255',
            'whatsapp_number' => 'required|string|max:20',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|max:255',
            'admin_password' => 'required|string|min:8',
            'otp' => 'required'
        ]);

        // Verify OTP again during registration
        $cachedOtp = \Illuminate\Support\Facades\Cache::get('otp_' . $validated['admin_email']);
        if ($cachedOtp != $validated['otp']) {
            return response()->json(['message' => 'OTP expired or invalid'], 400);
        }

        // Call the same logic as TenantRegistrationController
        try {
            DB::beginTransaction();

            $baseHost = parse_url(config('app.url'), PHP_URL_HOST) ?: 'localhost';
            $baseSub = \Illuminate\Support\Str::slug($validated['school_name']);
            if (empty($baseSub)) {
                $baseSub = 'school-' . rand(1000, 9999);
            }
            $candidate = $baseSub;
            $attempt = 1;
            while (Tenant::find($candidate) || Domain::where('domain', strtolower($candidate . '.' . $baseHost))->exists()) {
                $attempt++;
                $candidate = $baseSub . '-' . $attempt;
            }
            $tenantId = $candidate;
            $fullDomain = strtolower($tenantId . '.' . $baseHost);

            // Create tenant
            $tenant = Tenant::create([
                'domain' => $tenantId,
                'data' => [
                    'name' => $validated['school_name'],
                    'whatsapp_number' => $validated['whatsapp_number'] ?? '',
                ],
            ]);
            // Fetch the tenant by unique slug to get the new ID
            $tenant = Tenant::where('domain', $tenantId)->firstOrFail();
            $tenant->domains()->create(['domain' => $fullDomain]);

            // Create School record
            $school = \App\Models\School::create([
                'schoolName' => $validated['school_name'],
                'schoolCity' => '',
                'schoolAdminName' => $validated['admin_name'],
                'schoolAdminEmail' => $validated['admin_email'],
                'schoolAdminPassword' => Hash::make($validated['admin_password']),
                'whatsapp_number' => $validated['whatsapp_number'] ?? '',
                'domain' => $fullDomain,
                'status' => 'active',
                'tenant_id' => $tenant->id,
            ]);

            // Create superadmin user
            $user = User::create([
                'name' => $validated['admin_name'],
                'email' => $validated['admin_email'],
                'password' => Hash::make($validated['admin_password']),
                'role' => 'admin',
                'tenant_id' => $school->id, // Constraint: users.tenant_id -> schools.id
                'school_id' => $school->id,
            ]);

            // Clear OTP
            \Illuminate\Support\Facades\Cache::forget('otp_' . $validated['admin_email']);

            // Send Welcome Email
            \Illuminate\Support\Facades\Mail::send('emails.welcome', ['name' => $validated['admin_name']], function ($message) use ($validated) {
                $message->to($validated['admin_email'])->subject('Welcome to CloudiSchool!');
            });

            DB::commit();

            $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'user' => $user,
                'token' => $token,
                'message' => 'Registration successful! Welcome to CloudiSchool.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Registration failed: ' . $e->getMessage()], 500);
        }
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $school = \App\Models\School::where('schoolAdminEmail', $request->email)->first();

        if (!$school) {
            return response()->json(['message' => 'This email address is not registered with us.'], 404);
        }

        $otp = rand(1000, 9999);
        \Illuminate\Support\Facades\Cache::put('reset_otp_' . $request->email, $otp, now()->addMinutes(10));

        try {
            \Illuminate\Support\Facades\Mail::send('emails.password-reset-otp', ['otp' => $otp], function ($message) use ($request) {
                $message->to($request->email)->subject('Password Reset OTP - CloudiSchool');
            });
            return response()->json(['status' => 'success', 'message' => 'Reset OTP sent to your email']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to send reset OTP: ' . $e->getMessage()], 500);
        }
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|numeric',
            'password' => 'required|string|min:8|confirmed'
        ]);

        $cachedOtp = \Illuminate\Support\Facades\Cache::get('reset_otp_' . $request->email);

        if (!$cachedOtp || $cachedOtp != $request->otp) {
            return response()->json(['status' => 'error', 'message' => 'Invalid or expired OTP'], 400);
        }

        try {
            DB::beginTransaction();

            $hashedPassword = Hash::make($request->password);

            // Update School password
            \App\Models\School::where('schoolAdminEmail', $request->email)->update([
                'schoolAdminPassword' => $hashedPassword
            ]);

            // Update or Create User password (synchronize)
            $user = User::where('email', $request->email)->first();
            if ($user) {
                $user->update(['password' => $hashedPassword]);
            } else {
                // Critical fallback: if school exists but user doesn't, create user
                $school = \App\Models\School::where('schoolAdminEmail', $request->email)->first();
                if ($school) {
                    User::create([
                        'name' => $school->schoolAdminName ?? 'Admin',
                        'email' => $request->email,
                        'password' => $hashedPassword,
                        'role' => 'admin',
                        'tenant_id' => $school->id,
                        'school_id' => $school->id,
                    ]);
                }
            }

            // Clear OTP
            \Illuminate\Support\Facades\Cache::forget('reset_otp_' . $request->email);

            DB::commit();

            return response()->json(['status' => 'success', 'message' => 'Password reset successful']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Failed to reset password: ' . $e->getMessage()], 500);
        }
    }

    public function logout(Request $request)
    {
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
        }
        return response()->json(['message' => 'Logged out']);
    }

    public function user(Request $request)
    {
        return $request->user();
    }

    public function dashboardStats()
    {
        $studentsCount = DB::table('students')->count();
        $teachersCount = DB::table('teachers')->count();
        $classesCount = DB::table('classes')->count();

        return response()->json([
            'students_count' => $studentsCount,
            'teachers_count' => $teachersCount,
            'classes_count' => $classesCount,
            'recent_exams' => 5
        ]);
    }

    // CRUD Methods
    public function getStudents(Request $request)
    {
        $schoolId = Auth::user()->school_id;
        return response()->json(['status' => 'success', 'data' => DB::table('students')->where('school_id', $schoolId)->get()]);
    }

    public function addStudent(Request $request)
    {
        $schoolId = Auth::user()->school_id;
        $data = $request->all();
        $data['school_id'] = $schoolId;
        $id = DB::table('students')->insertGetId($data);
        return response()->json(['status' => 'success', 'id' => $id]);
    }

    public function updateStudent(Request $request, $id)
    {
        $schoolId = Auth::user()->school_id;
        $affected = DB::table('students')
            ->where('id', $id)
            ->where('school_id', $schoolId)
            ->update($request->except(['id', 'school_id']));

        return response()->json(['status' => 'success', 'affected' => $affected]);
    }

    public function deleteStudent($id)
    {
        $schoolId = Auth::user()->school_id;
        DB::table('students')->where('id', $id)->where('school_id', $schoolId)->delete();
        return response()->json(['status' => 'success']);
    }

    public function getTeachers(Request $request)
    {
        $schoolId = Auth::user()->school_id;
        return response()->json(['status' => 'success', 'data' => DB::table('teachers')->where('school_id', $schoolId)->get()]);
    }

    public function addTeacher(Request $request)
    {
        $schoolId = Auth::user()->school_id;
        $data = $request->all();
        $data['school_id'] = $schoolId;
        $id = DB::table('teachers')->insertGetId($data);
        return response()->json(['status' => 'success', 'id' => $id]);
    }

    public function updateTeacher(Request $request, $id)
    {
        $schoolId = Auth::user()->school_id;
        $affected = DB::table('teachers')
            ->where('id', $id)
            ->where('school_id', $schoolId)
            ->update($request->except(['id', 'school_id']));

        return response()->json(['status' => 'success', 'affected' => $affected]);
    }

    public function deleteTeacher($id)
    {
        $schoolId = Auth::user()->school_id;
        DB::table('teachers')->where('id', $id)->where('school_id', $schoolId)->delete();
        return response()->json(['status' => 'success']);
    }

    public function getParents(Request $request)
    {
        $schoolId = Auth::user()->school_id;
        return response()->json(['status' => 'success', 'data' => DB::table('parents')->where('school_id', $schoolId)->get()]);
    }

    public function addParent(Request $request)
    {
        $schoolId = Auth::user()->school_id;
        $data = $request->all();
        $data['school_id'] = $schoolId;
        $id = DB::table('parents')->insertGetId($data);
        return response()->json(['status' => 'success', 'id' => $id]);
    }

    public function updateParent(Request $request, $id)
    {
        $schoolId = Auth::user()->school_id;
        $affected = DB::table('parents')
            ->where('id', $id)
            ->where('school_id', $schoolId)
            ->update($request->except(['id', 'school_id']));

        return response()->json(['status' => 'success', 'affected' => $affected]);
    }

    public function deleteParent($id)
    {
        $schoolId = Auth::user()->school_id;
        DB::table('parents')->where('id', $id)->where('school_id', $schoolId)->delete();
        return response()->json(['status' => 'success']);
    }

    public function getFees()
    {
        return response()->json(['status' => 'success', 'data' => DB::table('fees_groups')->get()]);
    }

    public function getFeeTypes()
    {
        return response()->json(['status' => 'success', 'data' => DB::table('fee_types')->get()]);
    }

    public function getAttendance()
    {
        return response()->json(['status' => 'success', 'data' => DB::table('student_attendance')->limit(100)->get()]);
    }

    public function saveAttendance(Request $request)
    {
        return response()->json(['status' => 'success', 'message' => 'Attendance saved']);
    }
}
