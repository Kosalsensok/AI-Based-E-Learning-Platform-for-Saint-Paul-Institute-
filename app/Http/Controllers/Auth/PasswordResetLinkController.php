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

        $user = User::where('email', $input)
            ->orWhere('student_code', $input)
            ->orWhere('phone', $input)
            ->first();

        if (!$user) {
            return back()->withErrors(['email' => 'រកមិនឃើញគណនីដែលប្រើព័ត៌មាននេះទេ។']);
        }

        // Generate 6-digit verification code
        $code = (string) rand(100000, 999999);

        // Store code in session for validation
        session([
            'reset_code'       => $code,
            'reset_user_id'    => $user->id,
            'reset_user_email' => $user->email,
        ]);

        // Send Telegram alert
        $telegramService->sendMessage(
            "<b>🔑 PASSWORD RESET REQUEST</b>\n" .
            "----------------------------------------\n" .
            "👤 <b>User:</b> {$user->name} ({$user->email})\n" .
            "🔢 <b>Verification Code:</b> <code>{$code}</code>\n" .
            "⏰ <b>Requested At:</b> " . now()->format('Y-m-d H:i:s') . "\n"
        );

        return back()->with([
            'status'     => "កូដផ្ទៀងផ្ទាត់ 6 ខ្ទង់ ({$code}) ត្រូវបានផ្ញើទៅកាន់ព័ត៌មានរបស់អ្នករួចរាល់ហើយ!",
            'demo_code'  => $code,
            'reset_user' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
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

        $user = User::where('email', $input)
            ->orWhere('student_code', $input)
            ->orWhere('phone', $input)
            ->first();

        if (!$user) {
            return back()->withErrors(['email' => 'រកមិនឃើញគណនីនេះទេ។']);
        }

        $sessionCode = session('reset_code');
        $sessionUserId = session('reset_user_id');

        if (!$sessionCode || $request->code !== $sessionCode || $sessionUserId != $user->id) {
            return back()->withErrors(['code' => 'កូដផ្ទៀងផ្ទាត់មិនត្រឹមត្រូវទេ! សូមពិនិត្យកូដ 6 ខ្ទង់ម្តងទៀត។']);
        }

        // Update password
        $user->update([
            'password'       => Hash::make($request->password),
            'locked_until'   => null,
            'login_attempts' => 0,
        ]);

        // Clear reset session
        session()->forget(['reset_code', 'reset_user_id', 'reset_user_email']);

        return redirect('/login')->with('status', 'ពាក្យសម្ងាត់ត្រូវបានផ្លាស់ប្តូរដោយជោគជ័យ! សូមចូលប្រើប្រាស់គណនីរបស់អ្នក។');
    }
}
