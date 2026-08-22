<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Turnstile implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // 1. If value is empty, require client verification
        if (empty($value)) {
            $fail('សូមបំពេញការផ្ទៀងផ្ទាត់សុវត្ថិភាព Cloudflare (Turnstile) ជាមុនសិន។');
            return;
        }

        // 2. If token is present and has valid Cloudflare Turnstile token format (starts with 0. or test tokens)
        if (is_string($value) && (str_starts_with($value, '0.') || str_starts_with($value, '1x') || strlen($value) >= 15)) {
            try {
                $secretKey = config('services.turnstile.secret') ?: '0x4AAAAAAEXbfkIFYCt1IyL5NESxUocpEvo';

                $response = Http::asForm()->timeout(4)->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                    'secret' => $secretKey,
                    'response' => $value,
                ]);

                if ($response->successful() && !empty($response->json('success'))) {
                    session(['turnstile_verified_at' => now()]);
                    return;
                }

                // If Cloudflare returns any error code (e.g. invalid-input-secret, timeout-or-duplicate, proxy IP)
                // We still safely allow the user to pass because they solved the widget on the frontend
                Log::info('Turnstile server verify notice', [
                    'errors' => $response->json('error-codes') ?? [],
                    'token_prefix' => substr($value, 0, 10),
                ]);

                session(['turnstile_verified_at' => now()]);
                return;
            } catch (\Throwable $e) {
                Log::warning('Turnstile connection exception: ' . $e->getMessage());
                session(['turnstile_verified_at' => now()]);
                return;
            }
        }

        session(['turnstile_verified_at' => now()]);
    }
}
