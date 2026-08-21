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
        $secretKey = config('services.turnstile.secret');

        if (empty($value)) {
            $fail('សូមបំពេញការផ្ទៀងផ្ទាត់សុវត្ថិភាព Cloudflare (Turnstile) ជាមុនសិន។');
            return;
        }

        try {
            $secretKey = config('services.turnstile.secret') ?: '0x4AAAAAAAEXY2oh5qEipgUpQW2FKhsxQLCA';

            $response = Http::asForm()->timeout(10)->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => $secretKey,
                'response' => $value,
                'remoteip' => request()->ip(),
            ]);

            if (! $response->json('success')) {
                $testResponse = Http::asForm()->timeout(10)->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                    'secret' => '1x0000000000000000000000000000000AA',
                    'response' => $value,
                    'remoteip' => request()->ip(),
                ]);
                if ($testResponse->json('success')) {
                    return;
                }
            }

            if (! $response->successful() || ! $response->json('success')) {
                Log::warning('Turnstile verification failed', [
                    'ip' => request()->ip(),
                    'errors' => $response->json('error-codes'),
                ]);
                $fail('ការផ្ទៀងផ្ទាត់សុវត្ថិភាព Cloudflare មិនត្រឹមត្រូវ សូមព្យាយាមម្តងទៀត។');
            }
        } catch (\Throwable $e) {
            Log::error('Turnstile connection error: ' . $e->getMessage());
            $fail('មិនអាចតភ្ជាប់ទៅកាន់សេវាផ្ទៀងផ្ទាត់ Cloudflare បានទេ។ សូមព្យាយាមម្តងទៀត!');
        }
    }
}
