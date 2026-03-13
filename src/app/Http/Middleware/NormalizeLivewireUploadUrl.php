<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Livewire dosya yükleme isteğinde Laravel'in gördüğü URL'yi APP_URL ile eşleştirir.
 * Proxy/tunnel (nginx, ngrok vb.) nedeniyle gelen istekte Host farklı olunca
 * imza doğrulaması 401 verir; bu middleware isteği APP_URL host'u ile normalize eder.
 */
class NormalizeLivewireUploadUrl
{
    public function handle(Request $request, Closure $next): Response
    {
        $appUrl = config('app.url');
        if (! $appUrl || ! $request->is('livewire/upload-file')) {
            return $next($request);
        }

        $parsed = parse_url($appUrl);
        $host = $parsed['host'] ?? '';
        $port = $parsed['port'] ?? null;
        $scheme = $parsed['scheme'] ?? 'http';
        if ($port && (($scheme === 'https' && $port != 443) || ($scheme === 'http' && $port != 80))) {
            $host .= ':' . $port;
        }

        $clone = $request->duplicate();
        $clone->server->set('HTTP_HOST', $host);
        $clone->headers->set('HOST', $host);
        $clone->server->set('REQUEST_SCHEME', $scheme);
        $clone->server->set('HTTPS', $scheme === 'https' ? 'on' : 'off');

        app()->instance('request', $clone);

        return $next($clone);
    }
}
