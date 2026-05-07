<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetStoreLocale
{
    public const SUPPORTED = ['tr', 'en', 'it'];

    public function handle(Request $request, Closure $next): Response
    {
        $default = config('app.locale');
        $fromSession = session('locale', $default);
        $locale = in_array((string) $fromSession, self::SUPPORTED, true)
            ? (string) $fromSession
            : (in_array((string) $default, self::SUPPORTED, true) ? (string) $default : 'tr');

        App::setLocale($locale);

        return $next($request);
    }
}
