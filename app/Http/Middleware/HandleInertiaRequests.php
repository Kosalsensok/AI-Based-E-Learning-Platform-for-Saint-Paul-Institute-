<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'telegram' => [
                'bot_username' => config('services.telegram.bot_username') ?? env('TELEGRAM_BOT_USERNAME', 'spi_elms_auth_bot'),
                'is_configured' => !empty(config('services.telegram.bot_token') ?? env('TELEGRAM_BOT_TOKEN')),
            ],
            'clerk' => [
                'publishable_key' => config('services.clerk.publishable_key') ?? env('VITE_CLERK_PUBLISHABLE_KEY'),
                'app_id' => config('services.clerk.app_id') ?? env('CLERK_APP_ID', 'app_3HuqsrwyUIBYDv90aKgOiwfsKkx'),
                'is_configured' => !empty(config('services.clerk.publishable_key') ?? env('VITE_CLERK_PUBLISHABLE_KEY')),
            ],
        ];
    }
}
