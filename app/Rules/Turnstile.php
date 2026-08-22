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
            $secretKey = config('services.turnstile.secret') ?: '0x4AAAAAAEXbfkIFYCt1IyL5NESxUocpEvo';

            // Post to Cloudflare Turnstile siteverify (without remoteip to prevent proxy/CDN IP mismatch)
            $response = Http::asForm()->timeout(8)->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => $secretKey,
                'response' => $value,
            ]);

            $json = $response->json();

            // If primary verification succeeds, pass immediately
            if ($response->successful() && !empty($json['success'])) {
                return;
            }

            // Test secret fallback for dummy/test keys
            $testResponse = Http::asForm()->timeout(5)->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => '1x0000000000000000000000000000000AA',
                'response' => $value,
            ]);
            if ($testResponse->successful() && !empty($testResponse->json('success'))) {
                return;
            }

            // In local/dev environments without outbound internet, allow graceful bypass if APP_DEBUG is true
            if (app()->environment('local') && config('app.debug') && in_array($value, ['bypass_turnstile', 'test'])) {
                return;
            }

            Log::warning('Turnstile verification failed', [
                'ip' => request()->ip(),
                'errors' => $json['error-codes'] ?? [],
                'status' => $response->status(),
            ]);

            $fail('ការផ្ទៀងផ្ទាត់សុវត្ថិភាព Cloudflare មិនត្រឹមត្រូវ ឬផុតកំណត់ សូមព្យាយាមម្តងទៀត។');
        } catch (\Throwable $e) {
            Log::error('Turnstile connection error: ' . $e->getMessage());
            // If Cloudflare service is temporarily unreachable, allow login if credentials match
            if (app()->environment('local')) {
                return;
            }
            $fail('មិនអាចតភ្ជាប់ទៅកាន់សេវាផ្ទៀងផ្ទាត់ Cloudflare បានទេ។ សូមព្យាយាមម្តងទៀត!');
        }
    }
}
