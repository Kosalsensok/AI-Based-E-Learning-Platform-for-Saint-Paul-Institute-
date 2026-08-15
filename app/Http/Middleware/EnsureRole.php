<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!$user->is_active) {
            abort(403, 'Account is disabled.');
        }

        // Admin has superuser access across teacher & admin modules
        if ($user->role === 'admin' || in_array($user->role, $roles)) {
            return $next($request);
        }

        abort(403, 'Unauthorized role access.');
    }
}
