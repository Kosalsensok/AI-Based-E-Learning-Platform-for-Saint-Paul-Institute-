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
        try {
            Cache::put('otp_' . $email, $otp, $expiresAt);
        } catch (\Throwable $e) {}

        // User Lookup or Early Creation for Infallible DB Storage
        $user = User::where('email', $email)->first();
        $recipientName = 'និស្សិត / សាស្ត្រាចារ្យ';

        if (!$user) {
            $studentCode = 'STU' . date('y') . rand(1000, 9999);
            while (User::where('student_code', $studentCode)->exists()) {
                $studentCode = 'STU' . date('y') . rand(1000, 9999);
            }

            $emailPrefix = explode('@', $email)[0];
            $formattedName = ucwords(str_replace(['.', '_', '-'], ' ', $emailPrefix));

            try {
                $user = User::create([
                    'name'              => $formattedName ?: 'Student',
                    'name_kh'           => $formattedName ?: 'Student',
                    'email'             => $email,
                    'password'          => Hash::make(Str::random(32)),
                    'role'              => 'student',
                    'student_code'      => $studentCode,
                    'study_type'        => 'on_campus',
                    'otp_code'          => $otp,
                    'otp_expires_at'    => $expiresAt,
                    'email_verified_at' => null,
                    'is_active'         => true,
                    'status'            => 'active',
                ]);
            } catch (\Throwable $e) {
                Log::warning('User auto-creation in sendEmailOtp: ' . $e->getMessage());
            }
            $recipientName = $formattedName ?: 'Student';
        } else {
            $recipientName = $user->name_kh ?: $user->name ?: 'User';
            try {
                $user->update([
                    'otp_code'       => $otp,
                    'otp_expires_at' => $expiresAt,
                ]);
            } catch (\Throwable $e) {
                Log::warning('OTP Database update note: ' . $e->getMessage());
            }
        }

        $resendApiKey = trim((string) (config('services.resend.key') ?: env('RESEND_API_KEY') ?: ''));
        if (str_contains($resendApiKey, 'ZET3HK1')) {
            $resendApiKey = str_replace('ZET3HK1', 'ZET3Hk1', $resendApiKey);
        }
        $fromAddress = config('mail.from.address') ?: env('MAIL_FROM_ADDRESS', 'info@spilms.tech');
        $fromName = config('mail.from.name') ?: env('MAIL_FROM_NAME', 'Saint Paul Institute (E-LMS)');
        $fromHeader = "{$fromName} <{$fromAddress}>";

        $subject = "[វិទ្យាស្ថាន សន្តប៉ូល] លេខកូដសម្ងាត់ OTP របស់អ្នកគឺ: {$otp}";

        $htmlContent = '
        <!DOCTYPE html>
        <html lang="km">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>OTP Code - Saint Paul Institute</title>
        </head>
        <body style="margin: 0; padding: 0; background-color: #0B132B; font-family: \'Segoe UI\', Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased;">
            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #0B132B; padding: 40px 15px;">
                <tr>
                    <td align="center">
                        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 540px; background: linear-gradient(180deg, #111D4A 0%, #0F172A 100%); border: 1px solid #1E293B; border-radius: 24px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.5);">
                            <!-- Header / Crest Banner -->
                            <tr>
                                <td style="padding: 35px 30px 20px 30px; text-align: center; background: linear-gradient(135deg, #1E3A8A 0%, #0284C7 100%);">
                                    <div style="display: inline-block; background-color: #FFFFFF; border-radius: 50%; padding: 6px; box-shadow: 0 4px 12px rgba(0,0,0,0.2); margin-bottom: 12px;">
                                        <img src="https://spilms.tech/images/logo.png" alt="Saint Paul Institute" width="60" height="60" style="display: block; border-radius: 50%; width: 60px; height: 60px; object-fit: contain;">
                                    </div>
                                    <h1 style="color: #FFFFFF; font-size: 22px; font-weight: 800; margin: 0 0 4px 0; letter-spacing: 0.5px;">
                                        វិទ្យាស្ថាន សន្តប៉ូល (SPI)
                                    </h1>
                                    <p style="color: #E0F2FE; font-size: 13px; font-weight: 500; margin: 0; letter-spacing: 0.3px;">
                                        Saint Paul Institute • AI-Powered E-Learning Platform
                                    </p>
                                </td>
                            </tr>
                            <!-- Body Content -->
                            <tr>
                                <td style="padding: 35px 35px 25px 35px; text-align: center;">
                                    <h2 style="color: #F8FAFC; font-size: 18px; font-weight: 700; margin: 0 0 12px 0;">
                                        លេខកូដសម្ងាត់ OTP សម្រាប់ចូលប្រើប្រព័ន្ធ
                                    </h2>
                                    <p style="color: #94A3B8; font-size: 14px; line-height: 1.6; margin: 0 0 25px 0;">
                                        សួស្តី <strong style="color: #38BDF8;">' . htmlspecialchars($recipientName) . '</strong>! សូមប្រើប្រាស់លេខកូដ ៦ ខ្ទង់ខាងក្រោម ដើម្បីផ្ទៀងផ្ទាត់ និងចូលប្រើប្រាស់គណនីរបស់អ្នកលើប្រព័ន្ធ SPI AI-ELMS៖
                                    </p>
                                    
                                    <!-- OTP Code Highlight Box -->
                                    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 0 auto 25px auto;">
                                        <tr>
                                            <td align="center">
                                                <div style="background: linear-gradient(135deg, #1D4ED8 0%, #2563EB 50%, #0284C7 100%); border-radius: 16px; padding: 18px 30px; display: inline-block; box-shadow: 0 8px 20px rgba(37, 99, 235, 0.35); border: 1px solid rgba(255,255,255,0.2);">
                                                    <span style="font-family: Consolas, monospace, \'Courier New\'; font-size: 36px; font-weight: 900; letter-spacing: 10px; color: #FFFFFF; display: block; text-shadow: 0 2px 4px rgba(0,0,0,0.3);">
                                                        ' . $otp . '
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>

                                    <!-- Expiry Warning -->
                                    <div style="background-color: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.25); border-radius: 12px; padding: 10px 15px; margin: 0 auto 25px auto; display: inline-block;">
                                        <p style="color: #F87171; font-size: 12px; font-weight: 600; margin: 0;">
                                            ⏱️ លេខកូដនេះមានសុពលភាពត្រឹមតែ <strong>៥ នាទី</strong> ប៉ុណ្ណោះ។
                                        </p>
                                    </div>

                                    <p style="color: #64748B; font-size: 12px; line-height: 1.5; margin: 0 0 10px 0;">
                                        ⚠️ ដើម្បីសុវត្ថិភាពគណនី សូមកុំចែករំលែកលេខកូដសម្ងាត់នេះទៅកាន់អ្នកដទៃជាដាច់ខាត។
                                    </p>
                                </td>
                            </tr>
                            <!-- Footer -->
                            <tr>
                                <td style="padding: 20px 30px; text-align: center; border-top: 1px solid #1E293B; background-color: #0A0F1D;">
                                    <p style="color: #475569; font-size: 11px; margin: 0 0 6px 0; line-height: 1.5;">
                                        📍 វិទ្យាស្ថាន សន្តប៉ូល • ភូមិអាំងតាសោម ឃុំអាំងតាសោម ស្រុកត្រាំកក់ ខេត្តតាកែវ
                                    </p>
                                    <p style="color: #475569; font-size: 11px; margin: 0;">
                                        © ' . date('Y') . ' Saint Paul Institute. All rights reserved. • <a href="https://spilms.tech" style="color: #38BDF8; text-decoration: none; font-weight: 600;">spilms.tech</a>
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>';

        $plainText = "វិទ្យាស្ថាន សន្តប៉ូល (Saint Paul Institute - SPI AI-ELMS)\n\nសួស្តី {$recipientName}!\n\nលេខកូដសម្ងាត់ OTP សម្រាប់ចូលប្រើប្រព័ន្ធរបស់អ្នកគឺ៖ {$otp}\n\nលេខកូដនេះមានសុពលភាព ៥ នាទីប៉ុណ្ណោះ។\n\n© Saint Paul Institute (https://spilms.tech)";

        $sent = false;
        $errorMsg = null;

        // 1. Send via Resend HTTP API (Direct & reliable across all platforms)
        if ($resendApiKey) {
            try {
                $response = Http::withoutVerifying()->timeout(15)->withToken($resendApiKey)->post('https://api.resend.com/emails', [
                    'from'    => $fromHeader,
                    'to'      => [$email],
                    'subject' => $subject,
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

            // 1.1 Infallible direct cURL fallback
            if (!$sent && function_exists('curl_init')) {
                try {
                    $ch = curl_init('https://api.resend.com/emails');
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                        'Authorization: Bearer ' . $resendApiKey,
                        'Content-Type: application/json',
                    ]);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                        'from'    => $fromHeader,
                        'to'      => [$email],
                        'subject' => $subject,
                        'html'    => $htmlContent,
                        'text'    => $plainText,
                    ]));
                    $resCurl = curl_exec($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);

                    if ($httpCode >= 200 && $httpCode < 300) {
                        $sent = true;
                    }
                } catch (\Throwable $curlEx) {
                    Log::warning("cURL direct fallback error: " . $curlEx->getMessage());
                }
            }
        }

        // 2. Fallback via Mail Facade
        if (!$sent) {
            try {
                Mail::raw($plainText, function ($message) use ($email, $fromAddress, $fromName, $subject) {
                    $message->to($email)
                            ->from($fromAddress, $fromName)
                            ->subject($subject);
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
            'otp'   => 'required|string',
        ]);

        $email = strtolower(trim($request->email));
        $otp = trim((string) $request->otp);

        $cachedOtp = trim((string) Cache::get('otp_' . $email));
        $user = User::where('email', $email)->first();

        $isValidOtp = false;

        // 1. Verify against Cache
        if (!empty($cachedOtp) && $cachedOtp === $otp) {
            $isValidOtp = true;
        }

        // 2. Verify against database fallback
        if (!$isValidOtp && $user && !empty($user->otp_code) && trim((string) $user->otp_code) === $otp) {
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
        try { Cache::forget('otp_' . $email); } catch (\Throwable $e) {}

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
