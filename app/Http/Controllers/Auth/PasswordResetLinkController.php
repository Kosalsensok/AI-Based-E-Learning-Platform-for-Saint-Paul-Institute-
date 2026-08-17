<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create()
    {
        return Inertia::render('Auth/ForgotPassword');
    }

    /**
     * Handle an incoming password reset link or verification code request.
     */
    public function store(Request $request, TelegramService $telegramService)
    {
        $input = trim($request->input('email') ?? $request->input('identifier') ?? '');

        if (empty($input)) {
            $msg = 'សូមបញ្ចូលអាសយដ្ឋានអ៊ីមែល, ID, ឬលេខទូរស័ព្ទ។';
            if ($request->is('api/*') || (!$request->header('X-Inertia') && $request->wantsJson())) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->withErrors(['email' => $msg]);
        }

        $user = User::where(function ($query) use ($input) {
            $query->where('email', $input)
                ->orWhere('student_code', $input)
                ->orWhere('phone', $input);

            $cleanId = ltrim($input, '#');
            if (is_numeric($cleanId)) {
                $query->orWhere('id', (int) $cleanId);
            }
        })->first();

        if (!$user) {
            $msg = 'រកមិនឃើញគណនីដែលប្រើព័ត៌មាននេះទេ!';
            if ($request->is('api/*') || (!$request->header('X-Inertia') && $request->wantsJson())) {
                return response()->json(['success' => false, 'message' => $msg], 404);
            }
            return back()->withErrors(['email' => $msg]);
        }

        // Generate 6-digit verification code
        $code = (string) rand(100000, 999999);
        $expiresAt = now()->addMinutes(5);

        // Store code in database and session for validation (valid for 5 minutes)
        try {
            $user->update([
                'otp_code' => $code,
                'otp_expires_at' => $expiresAt,
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('OTP database save note: ' . $e->getMessage());
        }

        session([
            'reset_code'       => $code,
            'reset_user_id'    => $user->id,
            'reset_user_email' => $user->email,
            'reset_expires_at' => $expiresAt->timestamp,
        ]);

        $telegramTargetId = $user->telegram_chat_id ?? $user->telegram_id ?? null;
        $sentDirectly = false;

        if (!empty($telegramTargetId)) {
            $sentDirectly = $telegramService->sendPasswordResetOtp($user, $code);
        }

        // Also broadcast to admin monitoring channel
        $telegramService->sendMessage(
            "<b>🔑 PASSWORD RESET REQUEST</b>\n" .
            "----------------------------------------\n" .
            "👤 <b>User:</b> {$user->name} ({$user->email})\n" .
            "🔢 <b>Verification Code (OTP):</b> <code>{$code}</code>\n" .
            "✈️ <b>Direct Telegram:</b> " . ($sentDirectly ? "✅ Sent to Telegram ID: {$telegramTargetId}" : "⚠️ Not linked to Telegram") . "\n" .
            "⏰ <b>Requested At:</b> " . now()->format('Y-m-d H:i:s') . "\n"
        );

        $hasTelegram = !empty($telegramTargetId);
        $botUsername = $telegramService->getBotUsername();
        $linkTelegramUrl = "https://t.me/{$botUsername}?start={$user->id}";

        $statusMsg = $sentDirectly
            ? "លេខកូដ OTP 6 ខ្ទង់ ត្រូវបានផ្ញើទៅកាន់ Telegram Bot របស់អ្នក (@{$botUsername}) រួចរាល់ហើយ!"
            : ($hasTelegram
                ? "លេខកូដ OTP ត្រូវបានផ្ញើទៅកាន់ Telegram របស់អ្នក។"
                : "លេខកូដផ្ទៀងផ្ទាត់ 6 ខ្ទង់ត្រូវបានបង្កើតរួចរាល់ហើយ!");

        if ($request->is('api/*') || (!$request->header('X-Inertia') && $request->wantsJson())) {
            return response()->json([
                'success'           => true,
                'message'           => $statusMsg,
                'sent_to_telegram'  => $sentDirectly,
                'has_telegram'      => $hasTelegram,
                'link_telegram_url' => $linkTelegramUrl,
                'telegram_url'      => $linkTelegramUrl,
                'telegram_bot_name' => $botUsername,
                'user'              => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                ],
            ]);
        }

        return back()->with([
            'success'           => true,
            'status'            => $statusMsg,
            'message'           => $statusMsg,
            'has_telegram'      => $hasTelegram,
            'sent_to_telegram'  => $sentDirectly,
            'telegram_bot_name' => $botUsername,
            'telegram_url'      => $linkTelegramUrl,
            'link_telegram_url' => $linkTelegramUrl,
            'reset_user'        => [
                'id'           => $user->id,
                'name'         => $user->name,
                'email'        => $user->email,
                'student_code' => $user->student_code,
                'telegram_id'  => $user->telegram_id,
            ],
        ]);
    }

    /**
     * Reset user password using verification code.
     */
    public function resetPassword(Request $request)
    {
        $input = trim($request->input('email') ?? $request->input('identifier') ?? '');
        $code = trim($request->input('code') ?? $request->input('otpCode') ?? '');
        $password = $request->input('password') ?? $request->input('newPassword') ?? '';
        $passwordConfirmation = $request->input('password_confirmation') ?? $password;

        if (empty($input) || empty($code) || empty($password)) {
            $msg = 'សូមបញ្ចូលព័ត៌មានឱ្យបានគ្រប់ជ្រុងជ្រោយ (អ៊ីមែល/ID, លេខកូដ OTP, និងពាក្យសម្ងាត់ថ្មី)។';
            if ($request->is('api/*') || (!$request->header('X-Inertia') && $request->wantsJson())) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->withErrors(['email' => $msg]);
        }

        if (strlen($password) < 8) {
            $msg = 'ពាក្យសម្ងាត់ត្រូវមានយ៉ាងតិច 8 តួអក្សរ។';
            if ($request->is('api/*') || (!$request->header('X-Inertia') && $request->wantsJson())) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->withErrors(['password' => $msg]);
        }

        if ($request->has('password_confirmation') && $password !== $passwordConfirmation) {
            $msg = 'ការបញ្ជាក់ពាក្យសម្ងាត់មិនត្រូវគ្នាទេ។';
            if ($request->is('api/*') || (!$request->header('X-Inertia') && $request->wantsJson())) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->withErrors(['password' => $msg]);
        }

        $user = User::where(function ($query) use ($input) {
            $query->where('email', $input)
                ->orWhere('student_code', $input)
                ->orWhere('phone', $input);

            $cleanId = ltrim($input, '#');
            if (is_numeric($cleanId)) {
                $query->orWhere('id', (int) $cleanId);
            }
        })->first();

        if (!$user) {
            $msg = 'រកមិនឃើញគណនីនេះទេ។';
            if ($request->is('api/*') || (!$request->header('X-Inertia') && $request->wantsJson())) {
                return response()->json(['success' => false, 'message' => $msg], 404);
            }
            return back()->withErrors(['email' => $msg]);
        }

        $sessionCode = session('reset_code');
        $sessionUserId = session('reset_user_id');
        $sessionExpiresAt = session('reset_expires_at');

        $isValidOtp = false;

        // 1. Check in database
        if (!empty($user->otp_code) && $user->otp_code === $code) {
            if (!$user->otp_expires_at || $user->otp_expires_at->isFuture()) {
                $isValidOtp = true;
            }
        }

        // 2. Check in session fallback
        if (!$isValidOtp && $sessionCode === $code && $sessionUserId == $user->id) {
            if (!$sessionExpiresAt || now()->timestamp <= $sessionExpiresAt) {
                $isValidOtp = true;
            }
        }

        if (!$isValidOtp) {
            $msg = 'លេខកូដ OTP មិនត្រឹមត្រូវ ឬផុតសុពលភាព (៥ នាទី)!';
            if ($request->is('api/*') || (!$request->header('X-Inertia') && $request->wantsJson())) {
                return response()->json(['success' => false, 'message' => $msg], 400);
            }
            return back()->withErrors(['code' => $msg]);
        }

        // Update password and clear OTP
        try {
            $user->update([
                'password'       => Hash::make($password),
                'otp_code'       => null,
                'otp_expires_at' => null,
                'locked_until'   => null,
                'login_attempts' => 0,
            ]);
        } catch (\Throwable $e) {
            $user->update([
                'password'       => Hash::make($password),
                'locked_until'   => null,
                'login_attempts' => 0,
            ]);
        }

        // Clear reset session
        session()->forget(['reset_code', 'reset_user_id', 'reset_user_email', 'reset_expires_at']);

        $successMsg = 'ផ្លាស់ប្តូរលេខសម្ងាត់ជោគជ័យ! លោកអ្នកអាច Login បាន។';

        if ($request->is('api/*') || (!$request->header('X-Inertia') && $request->wantsJson())) {
            return response()->json([
                'success'  => true,
                'message'  => $successMsg,
                'redirect' => '/login',
            ]);
        }

        return redirect('/login')->with('status', $successMsg);
    }
}
