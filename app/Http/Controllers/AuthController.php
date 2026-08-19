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
        } else {
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

        $subject = "[SPI E-LMS] {$otp} ជាលេខកូដសម្ងាត់ OTP របស់អ្នក";

        // Render Clean Enterprise Email Template
        try {
            $htmlContent = view('emails.otp', ['otp' => $otp, 'user' => $user])->render();
        } catch (\Throwable $viewEx) {
            Log::warning('Email view render warning: ' . $viewEx->getMessage());
            $htmlContent = "<div style='font-family: sans-serif; padding: 20px;'><h2>Saint Paul Institute (E-LMS)</h2><p>លេខកូដ OTP របស់អ្នកគឺ: <strong>{$otp}</strong> (មានសុពលភាព ៥ នាទី)</p></div>";
        }

        $plainText = "វិទ្យាស្ថាន សន្តប៉ូល (Saint Paul Institute - SPI AI-ELMS)\n\nសួស្តី " . ($user->name ?? 'អ្នកប្រើប្រាស់') . "!\n\nលេខកូដសម្ងាត់ OTP សម្រាប់ចូលប្រើប្រព័ន្ធរបស់អ្នកគឺ៖ {$otp}\n\nលេខកូដនេះមានសុពលភាព ៥ នាទីប៉ុណ្ណោះ។\n\n© Saint Paul Institute (https://spilms.tech)";

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
                Mail::send('emails.otp', ['otp' => $otp, 'user' => $user], function ($message) use ($email, $fromAddress, $fromName, $subject) {
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
