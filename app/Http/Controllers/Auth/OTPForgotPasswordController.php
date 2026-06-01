<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class OTPForgotPasswordController extends Controller
{
    public function show()
    {
        return view('auth.passwords.otp-reset');
    }

    public function sendOTP(Request $request)
    {
        try {
            $request->validate(['email' => 'required|email|exists:schools,schoolAdminEmail'], [
                'email.exists' => 'This email address is not registered with us.'
            ]);

            $email = $request->email;
            $otp = rand(1000, 9999);

            // Store OTP in cache for 10 minutes
            Cache::put('reset_otp_' . $email, $otp, now()->addMinutes(10));

            // Send Email
            Mail::send('emails.password-reset-otp', ['otp' => $otp], function ($message) use ($email, $otp) {
                $message->to($email)->subject($otp . ' Password Reset OTP - CloudiSchool');
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

    public function reset(Request $request)
    {
        try {
            $data = $request->validate([
                'email' => ['required', 'email', 'exists:schools,schoolAdminEmail'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
                'otp' => ['required', 'numeric'],
            ]);

            // Verify OTP
            $cachedOtp = Cache::get('reset_otp_' . $data['email']);
            if (!$cachedOtp || $cachedOtp != $data['otp']) {
                return response()->json(['status' => 'error', 'message' => 'Invalid or expired OTP'], 400);
            }

            DB::beginTransaction();

            $hashedPassword = Hash::make($data['password']);

            // Update School password
            School::where('schoolAdminEmail', $data['email'])->update([
                'schoolAdminPassword' => $hashedPassword
            ]);

            // Update or Create User password (synchronize)
            $user = User::where('email', $data['email'])->first();
            if ($user) {
                $user->update(['password' => $hashedPassword]);
            } else {
                // Critical fallback: if school exists but user doesn't, create user
                $school = School::where('schoolAdminEmail', $data['email'])->first();
                User::create([
                    'name' => $school->schoolAdminName ?? 'Admin',
                    'email' => $data['email'],
                    'password' => $hashedPassword,
                    'role' => 'admin',
                    'tenant_id' => $school->id,
                    'school_id' => $school->id,
                ]);
            }

            // Clear OTP
            Cache::forget('reset_otp_' . $data['email']);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Password reset successfully! You can now log in.',
                'redirect' => route('login')
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->validator->errors()->first()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Reset failed: ' . $e->getMessage()], 500);
        }
    }
}
