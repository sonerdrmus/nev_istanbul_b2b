<?php

namespace App\Http\Middleware;

use App\Support\StoreLocale;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetStoreLocale
{
    /** @deprecated Use StoreLocale::supported() */
    public const SUPPORTED = ['tr', 'en', 'it'];

    public function handle(Request $request, Closure $next): Response
    {
        $routeLocale = $request->route('locale');
        if (is_string($routeLocale) && StoreLocale::isSupported($routeLocale)) {
            StoreLocale::apply($routeLocale);

            return $next($request);
        }

        $fromSession = session('locale');
        if (is_string($fromSession) && StoreLocale::isSupported($fromSession)) {
            StoreLocale::apply($fromSession);

            return $next($request);
        }

        StoreLocale::apply(StoreLocale::default());

        return $next($request);
    }
}
