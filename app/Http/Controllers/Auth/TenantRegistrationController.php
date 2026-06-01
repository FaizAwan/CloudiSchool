<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Tenant;
use App\Models\School;
use Stancl\Tenancy\Database\Models\Domain;

class TenantRegistrationController extends Controller
{
    public function show()
    {
        return view('auth.register-tenant');
    }

    public function sendOTP(Request $request)
    {
        try {
            $request->validate([
                'email' => [
                    'required',
                    'email',
                    'unique:schools,schoolAdminEmail',
                    'unique:users,email'
                ]
            ], [
                'email.unique' => 'This email is already registered. If it\'s yours, you can <a href="' . route('password.otp.show') . '?email=' . $request->email . '" class="fw-bold">Reset Password</a> instead.'
            ]);
            $email = $request->email;
            $otp = rand(1000, 9999);

            // Store OTP in cache for 10 minutes
            Cache::put('otp_' . $email, $otp, now()->addMinutes(10));

            // Send Email
            Mail::send('emails.otp', ['otp' => $otp], function ($message) use ($email, $otp) {
                $message->to($email)->subject($otp . ' Registration OTP - CloudiSchool');
            });

            return response()->json(['status' => 'success', 'message' => 'OTP sent to your email']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->validator->errors()->first()
            ], 422);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to send OTP: ' . $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'school_name' => ['required', 'string', 'max:255'],
                'whatsapp_number' => ['required', 'string', 'max:20'],
                'admin_name' => ['required', 'string', 'max:255'],
                'admin_email' => ['required', 'email', 'max:255', 'unique:schools,schoolAdminEmail'],
                'admin_password' => ['required', 'string', 'min:8', 'confirmed'],
                'otp' => ['required', 'numeric'],
            ]);

            // Verify OTP
            $cachedOtp = Cache::get('otp_' . $data['admin_email']);
            if (!$cachedOtp || $cachedOtp != $data['otp']) {
                return response()->json(['status' => 'error', 'message' => 'Invalid or expired OTP'], 400);
            }

            // Early duplicate checks to avoid SQL integrity errors
            $baseHost = parse_url(config('app.url'), PHP_URL_HOST) ?: 'localhost';

            // Normalize and auto-ensure uniqueness
            $baseSub = Str::slug($data['school_name']);
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

            DB::beginTransaction();

            // Create tenant
            $tenant = Tenant::create([
                'domain' => $tenantId, // Store slug in domain column
                'data' => [
                    'name' => $data['school_name'],
                    'whatsapp_number' => $data['whatsapp_number'],
                ],
            ]);

            // Refresh to get the auto-incremented ID from DB
            // Fetch the tenant by unique slug to get the new ID
            $tenant = Tenant::where('domain', $tenantId)->firstOrFail();

            $tenant->domains()->create(['domain' => $fullDomain]);

            // Create School record (which seems to be the parent table for users)
            $school = School::create([
                'schoolName' => $data['school_name'],
                'schoolCity' => '', // Default value to satisfy database constraint
                'schoolAdminName' => $data['admin_name'],
                'schoolAdminEmail' => $data['admin_email'],
                'schoolAdminPassword' => Hash::make($data['admin_password']),
                'whatsapp_number' => $data['whatsapp_number'],
                'domain' => $fullDomain,
                'status' => 'active',
                'tenant_id' => $tenant->id, // Link to the 'tenants' table
            ]);

            // Create superadmin user linked to the School
            $newUser = User::query()->create([
                'name' => $data['admin_name'],
                'email' => $data['admin_email'],
                'password' => Hash::make($data['admin_password']),
                'role' => 'admin',
                'tenant_id' => $school->id, // Constraint: users.tenant_id -> schools.id
                'school_id' => $school->id, // redundancy based on user model fillable
            ]);

            // Send Welcome Email
            Mail::send('emails.welcome', ['name' => $data['admin_name']], function ($message) use ($data) {
                $message->to($data['admin_email'])->subject('Welcome to CloudiSchool!');
            });

            // Clear OTP
            Cache::forget('otp_' . $data['admin_email']);

            DB::commit();

            // Create a short-lived onboarding login token
            $token = Str::random(40);
            Cache::put('onboard_login_' . $token, (int) $newUser->id, now()->addMinutes(10));

            $onboardUrl = url('/onboard/login/' . $token);

            if ($request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'School registered successfully!',
                    'redirect' => $onboardUrl
                ]);
            }

            return redirect()->away($onboardUrl);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->validator->errors()->first()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json(['status' => 'error', 'message' => 'Registration failed: ' . $e->getMessage()], 500);
            }
            throw $e;
        }
    }
}
