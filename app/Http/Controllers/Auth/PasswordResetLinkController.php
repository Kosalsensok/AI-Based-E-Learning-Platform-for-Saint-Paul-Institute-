<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
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
        $request->validate([
            'email' => 'required|string',
        ], [
            'email.required' => 'សូមបញ្ចូលអាសយដ្ឋានអ៊ីមែល, ID, ឬលេខទូរស័ព្ទ។',
        ]);

        $input = trim($request->email);

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
            return back()->withErrors(['email' => 'រកមិនឃើញគណនីដែលប្រើព័ត៌មាននេះទេ។']);
        }

        // Generate 6-digit verification code
        $code = (string) rand(100000, 999999);

        // Store code in session for validation (valid for 5 minutes)
        session([
            'reset_code'       => $code,
            'reset_user_id'    => $user->id,
            'reset_user_email' => $user->email,
            'reset_expires_at' => now()->addMinutes(5)->timestamp,
        ]);

        // Send OTP directly to User's Telegram if linked
        $sentDirectly = false;
        if (!empty($user->telegram_id)) {
            $sentDirectly = $telegramService->sendPasswordResetOtp($user, $code);
        }

        // Also send Telegram alert to admin channel
        $telegramService->sendMessage(
            "<b>🔑 PASSWORD RESET REQUEST</b>\n" .
            "----------------------------------------\n" .
            "👤 <b>User:</b> {$user->name} ({$user->email})\n" .
            "🔢 <b>Verification Code (OTP):</b> <code>{$code}</code>\n" .
            "✈️ <b>Direct Telegram:</b> " . ($sentDirectly ? "✅ Sent to Telegram ID: {$user->telegram_id}" : "⚠️ Not linked to Telegram") . "\n" .
            "⏰ <b>Requested At:</b> " . now()->format('Y-m-d H:i:s') . "\n"
        );

        $hasTelegram = !empty($user->telegram_id);
        $botUsername = $telegramService->getBotUsername();
        $linkTelegramUrl = "https://t.me/{$botUsername}?start={$user->id}";

        $statusMsg = $sentDirectly
            ? "លេខកូដ OTP 6 ខ្ទង់ ត្រូវបានផ្ញើទៅកាន់ Telegram Bot របស់អ្នក (@{$botUsername}) រួចរាល់ហើយ!"
            : "លេខកូដផ្ទៀងផ្ទាត់ 6 ខ្ទង់ត្រូវបានបង្កើតរួចរាល់ហើយ!";

        return back()->with([
            'status'            => $statusMsg,
            'demo_code'         => $code,
            'has_telegram'      => $hasTelegram,
            'sent_to_telegram'  => $sentDirectly,
            'telegram_bot_name' => $botUsername,
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
        $request->validate([
            'email'                 => 'required|string',
            'code'                  => 'required|string|size:6',
            'password'              => ['required', 'confirmed', Rules\Password::min(8)],
        ], [
            'code.required'         => 'សូមបញ្ចូលកូដផ្ទៀងផ្ទាត់ 6 ខ្ទង់។',
            'code.size'             => 'កូដផ្ទៀងផ្ទាត់ត្រូវមាន 6 ខ្ទង់។',
            'password.confirmed'    => 'ការបញ្ជាក់ពាក្យសម្ងាត់មិនត្រូវគ្នាទេ។',
            'password.min'          => 'ពាក្យសម្ងាត់ត្រូវមានយ៉ាងតិច 8 តួអក្សរ។',
        ]);

        $input = trim($request->email);

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
            return back()->withErrors(['email' => 'រកមិនឃើញគណនីនេះទេ។']);
        }

        $sessionCode = session('reset_code');
        $sessionUserId = session('reset_user_id');
        $sessionExpiresAt = session('reset_expires_at');

        if (!$sessionCode || $request->code !== $sessionCode || $sessionUserId != $user->id) {
            return back()->withErrors(['code' => 'កូដផ្ទៀងផ្ទាត់មិនត្រឹមត្រូវទេ! សូមពិនិត្យកូដ 6 ខ្ទង់ម្តងទៀត។']);
        }

        if ($sessionExpiresAt && now()->timestamp > $sessionExpiresAt) {
            session()->forget(['reset_code', 'reset_user_id', 'reset_user_email', 'reset_expires_at']);
            return back()->withErrors(['code' => 'លេខកូដ OTP នេះបានផុតកំណត់សុពលភាព (៥ នាទី) ហើយ! សូមស្នើសុំលេខកូដថ្មី។']);
        }

        // Update password
        $user->update([
            'password'       => Hash::make($request->password),
            'locked_until'   => null,
            'login_attempts' => 0,
        ]);

        // Clear reset session
        session()->forget(['reset_code', 'reset_user_id', 'reset_user_email', 'reset_expires_at']);

        return redirect('/login')->with('status', 'ពាក្យសម្ងាត់ត្រូវបានផ្លាស់ប្តូរដោយជោគជ័យ! សូមចូលប្រើប្រាស់គណនីរបស់អ្នក។');
    }
}
