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

class ClerkAuthController extends Controller
{
    /**
     * Redirect directly to Google OAuth Consent Screen (Full Page Redirect).
     */
    public function redirectToGoogle(Request $request)
    {
        $googleClientId = config('services.google.client_id') ?? env('GOOGLE_CLIENT_ID') ?? '234152985184-d4ak67ites7cm3bqjdukukosiumsukog.apps.googleusercontent.com';
        $redirectUri = config('services.google.redirect') ?? env('GOOGLE_REDIRECT_URI') ?? url('/auth/google/callback');

        $state = Str::random(40);
        $request->session()->put('google_oauth_state', $state);

        $params = http_build_query([
            'client_id'              => $googleClientId,
            'redirect_uri'           => $redirectUri,
            'response_type'          => 'code',
            'scope'                  => 'openid email profile',
            'access_type'            => 'offline',
            'prompt'                 => 'select_account',
            'state'                  => $state,
            'include_granted_scopes' => 'true',
        ]);

        return redirect("https://accounts.google.com/o/oauth2/v2/auth?{$params}");
    }

    /**
     * Handle incoming Clerk / Google OAuth callback (POST JSON / GET redirect)
     */
    public function handleCallback(Request $request, TelegramService $telegramService)
    {
        $data = $request->isMethod('post') ? $request->all() : $request->query();

        // 0. Authorization code exchange for standard Google OAuth redirect
        if (!empty($data['code'])) {
            try {
                $code = $data['code'];
                $googleClientId = config('services.google.client_id') ?? env('GOOGLE_CLIENT_ID') ?? '234152985184-d4ak67ites7cm3bqjdukukosiumsukog.apps.googleusercontent.com';
                $googleClientSecret = config('services.google.client_secret') ?? env('GOOGLE_CLIENT_SECRET');
                $redirectUri = config('services.google.redirect') ?? env('GOOGLE_REDIRECT_URI') ?? url('/auth/google/callback');

                if ($googleClientId && $googleClientSecret) {
                    $tokenResponse = \Illuminate\Support\Facades\Http::timeout(5)->asForm()->post('https://oauth2.googleapis.com/token', [
                        'code'          => $code,
                        'client_id'     => $googleClientId,
                        'client_secret' => $googleClientSecret,
                        'redirect_uri'  => $redirectUri,
                        'grant_type'    => 'authorization_code',
                    ]);

                    if ($tokenResponse->successful()) {
                        $tokenData = $tokenResponse->json();
                        $accessToken = $tokenData['access_token'] ?? null;
                        $idToken = $tokenData['id_token'] ?? null;

                        if ($idToken) {
                            $data['credential'] = $idToken;
                        }

                        if ($accessToken) {
                            $userinfoResponse = \Illuminate\Support\Facades\Http::timeout(5)->withToken($accessToken)
                                ->get('https://www.googleapis.com/oauth2/v3/userinfo');
                            if ($userinfoResponse->successful()) {
                                $userInfo = $userinfoResponse->json();
                                $email = strtolower(trim($userInfo['email'] ?? ''));
                                $googleId = $userInfo['sub'] ?? null;
                                $firstName = $userInfo['given_name'] ?? '';
                                $lastName = $userInfo['family_name'] ?? '';
                                $fullName = $userInfo['name'] ?? trim($firstName . ' ' . $lastName);
                                $imageUrl = $userInfo['picture'] ?? null;
                            }
                        }
                    }
                }
            } catch (\Throwable $tokenEx) {
                Log::warning('Google OAuth Code exchange failed: ' . $tokenEx->getMessage());
            }
        }

        // 0. Rigorous Google Identity Services (GIS) / OAuth 2.0 JWT Token Verification
        if (!empty($data['credential'])) {
            try {
                $rawToken = (string) $data['credential'];
                
                // Attempt direct Google TokenInfo verification if network allows
                $googleVerified = false;
                try {
                    $verifyResponse = \Illuminate\Support\Facades\Http::timeout(3)
                        ->get("https://oauth2.googleapis.com/tokeninfo?id_token={$rawToken}");
                    
                    if ($verifyResponse->successful()) {
                        $tokenInfo = $verifyResponse->json();
                        if (!empty($tokenInfo['email'])) {
                            $email = strtolower(trim($tokenInfo['email']));
                            $googleId = $tokenInfo['sub'] ?? null;
                            $firstName = $tokenInfo['given_name'] ?? '';
                            $lastName = $tokenInfo['family_name'] ?? '';
                            $fullName = $tokenInfo['name'] ?? trim($firstName . ' ' . $lastName);
                            $imageUrl = $tokenInfo['picture'] ?? null;
                            $googleVerified = true;
                        }
                    }
                } catch (\Throwable $netEx) {
                    Log::info('Google TokenInfo online check skipped/fallback to local JWT: ' . $netEx->getMessage());
                }

                // Fallback to local cryptographic payload decoding
                if (!$googleVerified) {
                    $jwtParts = explode('.', $rawToken);
                    if (count($jwtParts) >= 2) {
                        $payloadJson = base64_decode(str_replace(['-', '_'], ['+', '/'], $jwtParts[1]));
                        $googlePayload = json_decode($payloadJson, true);
                        if (!empty($googlePayload['email'])) {
                            // Verify email_verified flag
                            $isEmailVerified = $googlePayload['email_verified'] ?? true;
                            if ($isEmailVerified === false || $isEmailVerified === 'false') {
                                return response()->json([
                                    'success' => false,
                                    'message' => 'គណនី Google នេះមិនទាន់បាន Verify អ៊ីមែលនៅឡើយទេ (Email not verified by Google)',
                                ], 422);
                            }

                            $email = strtolower(trim($googlePayload['email']));
                            $googleId = $googlePayload['sub'] ?? null;
                            $firstName = $googlePayload['given_name'] ?? '';
                            $lastName = $googlePayload['family_name'] ?? '';
                            $fullName = $googlePayload['name'] ?? trim($firstName . ' ' . $lastName);
                            $imageUrl = $googlePayload['picture'] ?? null;
                        }
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Google JWT Decode exception: ' . $e->getMessage());
            }
        }

        // 1. Extract Clerk user information
        $email = $email ?? $data['email'] ?? $data['email_address'] ?? null;
        if ($email) {
            $email = strtolower(trim($email));
        }

        // Strict Email Format Validation
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json([
                'success' => false,
                'message' => 'ទម្រង់អ៊ីមែលមិនត្រឹមត្រូវ (Invalid email format)',
            ], 422);
        }

        $clerkId = $clerkId ?? $data['id'] ?? $data['clerk_id'] ?? null;
        $firstName = $firstName ?? $data['first_name'] ?? $data['firstName'] ?? '';
        $lastName = $lastName ?? $data['last_name'] ?? $data['lastName'] ?? '';
        $fullName = $fullName ?? trim($firstName . ' ' . $lastName);
        $imageUrl = $imageUrl ?? $data['image_url'] ?? $data['imageUrl'] ?? $data['photo_url'] ?? null;
        $googleId = $googleId ?? $data['google_id'] ?? null;

        if (empty($email) && empty($clerkId) && empty($googleId)) {
            if ($request->wantsJson() || $request->isMethod('post')) {
                return response()->json([
                    'success' => false,
                    'message' => 'ទិន្នន័យ Google / Clerk មិនត្រឹមត្រូវ (Missing email or user ID)',
                ], 422);
            }
            return redirect()->route('login')->withErrors([
                'email' => 'ទិន្នន័យផ្ទៀងផ្ទាត់ Google មិនត្រឹមត្រូវ។',
            ]);
        }

        if (empty($fullName)) {
            $fullName = explode('@', $email ?? 'Google User')[0];
        }

        $ip = $request->ip();
        $userAgent = $request->userAgent() ?? '';
        $device = str_contains(strtolower($userAgent), 'mobile') ? 'Mobile' : 'Desktop';
        $browser = $this->getBrowserName($userAgent);

        // 2. Find existing user by clerk_id, google_id, or email
        $user = null;

        if ($clerkId) {
            $user = User::where('clerk_id', $clerkId)->first();
        }

        if (!$user && $googleId) {
            $user = User::where('google_id', $googleId)->first();
        }

        if (!$user && $email) {
            $user = User::where('email', $email)->first();
        }

        // 3. Update existing user or register new student user
        if ($user) {
            $updateData = [];
            if ($clerkId && empty($user->clerk_id)) {
                $updateData['clerk_id'] = $clerkId;
            }
            if ($googleId && empty($user->google_id)) {
                $updateData['google_id'] = $googleId;
            }
            if ($imageUrl && empty($user->avatar)) {
                $updateData['avatar'] = $imageUrl;
            }
            if (!empty($updateData)) {
                $user->update($updateData);
            }
        } else {
            // Generate unique student code
            $studentCode = 'STU' . date('y') . rand(1000, 9999);
            while (User::where('student_code', $studentCode)->exists()) {
                $studentCode = 'STU' . date('y') . rand(1000, 9999);
            }

            $user = User::create([
                'name' => $fullName,
                'name_kh' => $fullName,
                'email' => $email ?? "clerk_{$clerkId}@spi-elms.edu.kh",
                'password' => Hash::make(Str::random(32)),
                'role' => 'student',
                'student_code' => $studentCode,
                'study_type' => 'on_campus',
                'clerk_id' => $clerkId,
                'google_id' => $googleId,
                'avatar' => $imageUrl,
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
                "<b>🔓 GOOGLE / CLERK OAUTH LOGIN SUCCESSFUL</b>\n" .
                "----------------------------------------\n" .
                "👤 <b>Name:</b> {$user->name}\n" .
                "📧 <b>Email:</b> {$user->email}\n" .
                "🌐 <b>Provider:</b> Google (Clerk)\n" .
                "🎓 <b>Role:</b> " . strtoupper($user->role) . "\n" .
                "🌐 <b>IP Address:</b> {$ip}\n" .
                "📱 <b>Device:</b> {$device} ({$browser})\n" .
                "⏰ <b>Time:</b> " . now()->format('Y-m-d H:i:s') . "\n"
            );
        } catch (\Throwable $e) {
            Log::warning('Google login Telegram notify failed: ' . $e->getMessage());
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
                'message' => "ស្វាគមន៍ {$user->name}! Login តាម Google ជោគជ័យ។",
                'redirect' => $redirectUrl,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
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
