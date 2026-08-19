<?php

namespace App\Http\Controllers;

use App\Models\AuthLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Send 6-digit OTP to user's Email via Resend.
     * Supports both existing users and new Gmail users (Auto-Registration).
     */
    public function sendEmailOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = strtolower(trim($request->email));

        // Generate 6-digit random code
        $otp = (string) rand(100000, 999999);
        $expiresAt = now()->addMinutes(5);

        // Store OTP in Cache (valid for 5 minutes)
        Cache::put('otp_' . $email, $otp, $expiresAt);

        // If user already exists, update OTP on model
        $user = User::where('email', $email)->first();
        if ($user) {
            try {
                $user->update([
                    'otp_code' => $otp,
                    'otp_expires_at' => $expiresAt,
                ]);
            } catch (\Throwable $e) {
                Log::warning('OTP Database update note: ' . $e->getMessage());
            }
        }

        $resendApiKey = config('services.resend.key') ?: env('RESEND_API_KEY');
        $fromAddress = config('mail.from.address') ?: env('MAIL_FROM_ADDRESS', 'info@spilms.tech');
        $fromName = config('mail.from.name') ?: env('MAIL_FROM_NAME', 'Saint Paul Institute (E-LMS)');
        $fromHeader = "{$fromName} <{$fromAddress}>";

        $htmlContent = '
        <div style="font-family: \'Segoe UI\', Arial, sans-serif; background-color: #0F172A; color: #F8FAFC; padding: 40px 20px; text-align: center;">
            <div style="max-width: 520px; margin: 0 auto; background-color: #1E293B; border-radius: 20px; padding: 35px; border: 1px solid #334155; box-shadow: 0 10px 25px rgba(0,0,0,0.3);">
                <div style="margin-bottom: 20px;">
                    <h2 style="color: #38BDF8; margin: 0; font-size: 24px; font-weight: 800; letter-spacing: 0.5px;">🎓 វិទ្យាស្ថាន សន្តប៉ូល</h2>
                    <p style="color: #94A3B8; font-size: 13px; margin: 5px 0 0 0;">Saint Paul Institute (SPI AI-ELMS)</p>
                </div>
                <hr style="border: none; border-top: 1px solid #334155; margin: 20px 0;" />
                <h3 style="color: #FFFFFF; font-size: 18px; margin-bottom: 10px;">លេខកូដសម្ងាត់ OTP សម្រាប់ Login</h3>
                <p style="color: #CBD5E1; font-size: 14px; line-height: 1.6; margin: 0 0 25px 0;">
                    សូមប្រើប្រាស់លេខកូដ ៦ ខ្ទង់ខាងក្រោម ដើម្បីផ្ទៀងផ្ទាត់ និងចូលប្រើប្រាស់ប្រព័ន្ធ E-LMS៖
                </p>
                <div style="background: linear-gradient(135deg, #1E40AF 0%, #3B82F6 100%); color: #FFFFFF; font-size: 32px; font-weight: 900; letter-spacing: 8px; padding: 18px 25px; border-radius: 14px; display: inline-block; margin-bottom: 25px; box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);">
                    ' . $otp . '
                </div>
                <p style="color: #EF4444; font-size: 12px; font-weight: bold; margin: 0 0 20px 0;">
                    ⏱️ លេខកូដនេះមានសុពលភាពត្រឹមតែ ៥ នាទីប៉ុណ្ណោះ។
                </p>
                <p style="color: #64748B; font-size: 11px; margin: 0; line-height: 1.5;">
                    ប្រសិនបើលោកអ្នកមិនបានស្នើសុំលេខកូដនេះទេ សូមកុំចែករំលែកលេខកូដនេះឱ្យអ្នកដទៃជាដាច់ខាត។
                </p>
                <hr style="border: none; border-top: 1px solid #334155; margin: 25px 0 15px 0;" />
                <p style="color: #475569; font-size: 11px; margin: 0;">
                    © ' . date('Y') . ' Saint Paul Institute. All rights reserved. • <a href="https://spilms.tech" style="color: #38BDF8; text-decoration: none;">spilms.tech</a>
                </p>
            </div>
        </div>';

        $plainText = "លេខកូដសម្ងាត់ OTP សម្រាប់ចូលប្រើប្រព័ន្ធ E-LMS របស់អ្នកគឺ៖ {$otp}\n\nលេខកូដនេះមានសុពលភាព ៥ នាទីប៉ុណ្ណោះ។\n\n© Saint Paul Institute (https://spilms.tech)";

        $sent = false;
        $errorMsg = null;

        // 1. Send via Resend HTTP API (Direct & reliable across all platforms)
        if ($resendApiKey) {
            try {
                $response = Http::withoutVerifying()->timeout(15)->withToken($resendApiKey)->post('https://api.resend.com/emails', [
                    'from'    => $fromHeader,
                    'to'      => [$email],
                    'subject' => 'លេខកូដសម្ងាត់ OTP - វិទ្យាស្ថាន សន្តប៉ូល (SPI E-LMS)',
                    'html'    => $htmlContent,
                    'text'    => $plainText,
                ]);

                if ($response->successful()) {
                    $sent = true;
                } else {
                    $errorMsg = $response->body();
                    Log::warning("Resend HTTP API Response error: " . $errorMsg);
                }
            } catch (\Throwable $resendEx) {
                $errorMsg = $resendEx->getMessage();
                Log::warning("Resend HTTP API exception: " . $errorMsg);
            }
        }

        // 2. Fallback via Resend Laravel Package / Mail Facade
        if (!$sent) {
            try {
                Mail::raw($plainText, function ($message) use ($email, $fromAddress, $fromName) {
                    $message->to($email)
                            ->from($fromAddress, $fromName)
                            ->subject('លេខកូដសម្ងាត់ OTP - វិទ្យាស្ថាន សន្តប៉ូល');
                });
                $sent = true;
            } catch (\Throwable $mailEx) {
                Log::error("Mail fallback sending error: " . $mailEx->getMessage());
                if (!$errorMsg) {
                    $errorMsg = $mailEx->getMessage();
                }
            }
        }

        if ($sent) {
            return response()->json([
                'success' => true,
                'message' => 'លេខកូដ OTP ត្រូវបានផ្ញើចូលប្រអប់សំបុត្រ Gmail របស់អ្នកហើយ! សូមពិនិត្យមើល Inbox/Spam។',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'មិនអាចផ្ញើ Email បានទេ៖ ' . ($errorMsg ?: 'Connection timeout'),
        ], 500);
    }

    /**
     * Verify 6-digit OTP and login or auto-register user.
     */
    public function verifyEmailOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|string|size:6',
        ]);

        $email = strtolower(trim($request->email));
        $otp = trim($request->otp);

        $cachedOtp = Cache::get('otp_' . $email);
        $user = User::where('email', $email)->first();

        $isValidOtp = false;

        // 1. Verify against Cache
        if ($cachedOtp && (string) $cachedOtp === $otp) {
            $isValidOtp = true;
        }

        // 2. Verify against database fallback
        if (!$isValidOtp && $user && !empty($user->otp_code) && (string) $user->otp_code === $otp) {
            if (!$user->otp_expires_at || $user->otp_expires_at->isFuture()) {
                $isValidOtp = true;
            }
        }

        if (!$isValidOtp) {
            return response()->json([
                'success' => false,
                'message' => 'លេខកូដ OTP មិនត្រឹមត្រូវ ឬផុតកំណត់ ៥ នាទីហើយ!',
            ], 422);
        }

        // Clean up OTP from cache
        Cache::forget('otp_' . $email);

        // 3. User Resolution: Find or Auto-Create Student Account
        if (!$user) {
            $studentCode = 'STU' . date('y') . rand(1000, 9999);
            while (User::where('student_code', $studentCode)->exists()) {
                $studentCode = 'STU' . date('y') . rand(1000, 9999);
            }

            $emailPrefix = explode('@', $email)[0];
            $formattedName = ucwords(str_replace(['.', '_', '-'], ' ', $emailPrefix));

            $user = User::create([
                'name'              => $formattedName ?: 'Student',
                'name_kh'           => $formattedName ?: 'Student',
                'email'             => $email,
                'password'          => Hash::make(Str::random(32)),
                'role'              => 'student',
                'student_code'      => $studentCode,
                'study_type'        => 'on_campus',
                'email_verified_at' => now(),
                'is_active'         => true,
                'status'            => 'active',
            ]);
        } else {
            try {
                $user->update([
                    'otp_code'          => null,
                    'otp_expires_at'    => null,
                    'email_verified_at' => $user->email_verified_at ?: now(),
                    'is_active'         => true,
                    'status'            => 'active',
                    'login_attempts'    => 0,
                ]);
            } catch (\Throwable $e) {}
        }

        // Log user into Laravel session
        if ($request->hasSession()) {
            $request->session()->regenerate();
        }
        Auth::login($user, true);
        if ($request->hasSession()) {
            $request->session()->save();
        }

        // Create Sanctum API Token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Record AuthLog
        try {
            AuthLog::create([
                'user_id'    => $user->id,
                'email'      => $user->email,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent() ?? '',
                'device'     => str_contains(strtolower($request->userAgent() ?? ''), 'mobile') ? 'Mobile' : 'Desktop',
                'browser'    => 'Browser (Email OTP)',
                'status'     => 'success',
            ]);
        } catch (\Throwable $e) {}

        $redirectUrl = match ($user->role) {
            'admin'   => '/admin/dashboard',
            'teacher' => '/teacher/dashboard',
            default   => '/student/dashboard',
        };

        return response()->json([
            'success'  => true,
            'message'  => 'ផ្ទៀងផ្ទាត់ OTP ត្រឹមត្រូវ! កំពុងនាំអ្នកទៅកាន់ Dashboard...',
            'token'    => $token,
            'user'     => $user,
            'redirect' => $redirectUrl,
        ]);
    }
}
