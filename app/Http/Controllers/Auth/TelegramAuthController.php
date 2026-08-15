<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuthLog;
use App\Models\User;
use App\Services\TelegramService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TelegramAuthController extends Controller
{
    /**
     * Handle incoming Telegram OAuth callback (POST JSON / GET redirect)
     */
    public function handleCallback(Request $request, TelegramService $telegramService)
    {
        $data = $request->isMethod('post') ? $request->all() : $request->query();

        // 1. Check if required Telegram OAuth ID is present
        if (empty($data['id'])) {
            if ($request->wantsJson() || $request->isMethod('post')) {
                return response()->json([
                    'success' => false,
                    'message' => 'ទិន្នន័យ Telegram មិនត្រឹមត្រូវ (Missing Telegram User ID)',
                ], 422);
            }
            return redirect()->route('login')->withErrors([
                'email' => 'ទិន្នន័យផ្ទៀងផ្ទាត់ Telegram មិនត្រឹមត្រូវ។',
            ]);
        }

        $ip = $request->ip();
        $userAgent = $request->userAgent() ?? '';
        $device = str_contains(strtolower($userAgent), 'mobile') ? 'Mobile' : 'Desktop';
        $browser = $this->getBrowserName($userAgent);

        // 2. Signature verification
        $isBotConfigured = !empty($telegramService->getBotToken());
        $isValid = false;

        if ($isBotConfigured && !empty($data['hash'])) {
            $isValid = $telegramService->verifyTelegramAuth($data);
        } elseif (!$isBotConfigured) {
            // If Bot token is not configured yet in local environment, allow for simulation/testing
            Log::info("Telegram Auth: Bot token not set in .env, executing in development sandbox mode.", ['data' => $data]);
            $isValid = true;
        }

        if (!$isValid) {
            AuthLog::create([
                'email' => $data['username'] ?? ('tg_' . ($data['id'] ?? 'unknown')),
                'ip_address' => $ip,
                'user_agent' => $userAgent,
                'device' => $device,
                'browser' => $browser,
                'status' => 'failed',
            ]);

            $errMsg = 'ការផ្ទៀងផ្ទាត់ហត្ថលេខា Telegram (Hash) មិនត្រឹមត្រូវទេ។';

            if ($request->wantsJson() || $request->isMethod('post')) {
                return response()->json([
                    'success' => false,
                    'message' => $errMsg,
                ], 401);
            }

            return redirect()->route('login')->withErrors(['email' => $errMsg]);
        }

        // 3. User Resolution: Find or Register User
        $telegramId = (string) $data['id'];
        $telegramUsername = $data['username'] ?? null;
        $firstName = $data['first_name'] ?? 'Telegram';
        $lastName = $data['last_name'] ?? '';
        $fullName = trim($firstName . ' ' . $lastName);
        $photoUrl = $data['photo_url'] ?? null;

        $user = User::where('telegram_id', $telegramId)->first();

        // If not found by telegram_id, try matching by telegram_username or email if exists
        if (!$user && $telegramUsername) {
            $user = User::where('telegram_username', $telegramUsername)
                ->orWhere('email', "{$telegramUsername}@telegram.spi-elms.edu.kh")
                ->first();
        }

        if ($user) {
            // Update Telegram profile info
            $updateData = [
                'telegram_id' => $telegramId,
            ];
            if ($telegramUsername) {
                $updateData['telegram_username'] = $telegramUsername;
            }
            if ($photoUrl) {
                $updateData['telegram_photo_url'] = $photoUrl;
                if (empty($user->avatar)) {
                    $updateData['avatar'] = $photoUrl;
                }
            }
            $user->update($updateData);
        } else {
            // Create a new Student account
            $email = $telegramUsername
                ? "{$telegramUsername}@telegram.spi-elms.edu.kh"
                : "tg_{$telegramId}@telegram.spi-elms.edu.kh";

            // Ensure unique email
            $counter = 1;
            $baseEmail = $email;
            while (User::where('email', $email)->exists()) {
                $email = str_replace('@', "_{$counter}@", $baseEmail);
                $counter++;
            }

            // Generate unique student code
            $studentCode = 'STU' . date('y') . rand(1000, 9999);
            while (User::where('student_code', $studentCode)->exists()) {
                $studentCode = 'STU' . date('y') . rand(1000, 9999);
            }

            $user = User::create([
                'name' => $fullName,
                'name_kh' => $fullName,
                'email' => $email,
                'password' => Hash::make(Str::random(32)),
                'role' => 'student',
                'student_code' => $studentCode,
                'study_type' => 'on_campus',
                'telegram_id' => $telegramId,
                'telegram_username' => $telegramUsername,
                'telegram_photo_url' => $photoUrl,
                'avatar' => $photoUrl,
                'is_active' => true,
                'status' => 'active',
            ]);
        }

        // 4. Account Status Verification
        if ($user->is_active === false || $user->status === 'inactive') {
            $errMsg = 'គណនីរបស់អ្នកត្រូវបានបិទដំណើរការ។';
            if ($request->wantsJson() || $request->isMethod('post')) {
                return response()->json(['success' => false, 'message' => $errMsg], 403);
            }
            return redirect()->route('login')->withErrors(['email' => $errMsg]);
        }

        if ($user->status === 'suspended') {
            $errMsg = 'គណនីរបស់អ្នកត្រូវបានព្យួរជាបណ្តោះអាសន្ន។';
            if ($request->wantsJson() || $request->isMethod('post')) {
                return response()->json(['success' => false, 'message' => $errMsg], 403);
            }
            return redirect()->route('login')->withErrors(['email' => $errMsg]);
        }

        // 5. Successful Authentication
        $user->update([
            'login_attempts' => 0,
            'locked_until' => null,
            'last_login_at' => now(),
        ]);

        AuthLog::create([
            'user_id' => $user->id,
            'email' => $user->email,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'device' => $device,
            'browser' => $browser,
            'status' => 'success',
        ]);

        // Security notification via Telegram
        try {
            $telegramService->sendMessage(
                "<b>🔓 TELEGRAM OAUTH LOGIN SUCCESSFUL</b>\n" .
                "----------------------------------------\n" .
                "👤 <b>User:</b> {$user->name}\n" .
                "✈️ <b>Telegram:</b> @" . ($telegramUsername ?? $telegramId) . "\n" .
                "🎓 <b>Role:</b> " . strtoupper($user->role) . "\n" .
                "🌐 <b>IP Address:</b> {$ip}\n" .
                "📱 <b>Device:</b> {$device} ({$browser})\n" .
                "⏰ <b>Time:</b> " . now()->format('Y-m-d H:i:s') . "\n"
            );
        } catch (\Throwable $e) {
            Log::warning('Telegram login notify failed: ' . $e->getMessage());
        }

        // Generate JWT Token safely if configured
        try {
            if (config('jwt.secret')) {
                $token = \Tymon\JWTAuth\Facades\JWTAuth::fromUser($user);
                $payload = \Tymon\JWTAuth\Facades\JWTAuth::setToken($token)->getPayload();

                \App\Models\JwtSession::create([
                    'user_id' => $user->id,
                    'token' => $token,
                    'expires_at' => Carbon::createFromTimestamp($payload->get('exp')),
                    'ip_address' => $ip,
                    'user_agent' => $userAgent,
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('JWT token creation exception: ' . $e->getMessage());
        }

        if ($request->hasSession()) {
            $request->session()->regenerate();
        }
        Auth::login($user, true);

        $redirectUrl = match ($user->role) {
            'admin' => '/admin/dashboard',
            'teacher' => '/teacher/dashboard',
            default => '/student/dashboard',
        };

        if ($request->wantsJson() || $request->isMethod('post')) {
            return response()->json([
                'success' => true,
                'message' => "ស្វាគមន៍ {$user->name}! Login ជោគជ័យ។",
                'redirect' => $redirectUrl,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'role' => $user->role,
                    'avatar' => $user->avatar,
                ],
            ]);
        }

        return redirect()->intended($redirectUrl);
    }

    private function getBrowserName($userAgent)
    {
        if (str_contains($userAgent, 'Chrome'))
            return 'Chrome';
        if (str_contains($userAgent, 'Firefox'))
            return 'Firefox';
        if (str_contains($userAgent, 'Safari'))
            return 'Safari';
        if (str_contains($userAgent, 'Edge'))
            return 'Edge';
        return 'Unknown';
    }
}
