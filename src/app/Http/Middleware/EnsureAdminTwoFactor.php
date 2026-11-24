<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminTwoFactor
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('store.admin_force_2fa', true)) {
            return $next($request);
        }

        $user = $request->user();
        if (! $user || ! $user->is_admin) {
            return $next($request);
        }

        if ($request->routeIs('filament.admin.auth.*') || $request->routeIs('admin.2fa.*')) {
            return $next($request);
        }

        if (! $user->hasTwoFactorEnabled()) {
            return redirect()->route('admin.2fa.setup');
        }

        if (! $request->session()->get('admin_2fa_passed')) {
            return redirect()->route('admin.2fa.challenge');
        }

        return $next($request);
    }
}
