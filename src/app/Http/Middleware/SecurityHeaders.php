<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Store + panel için temel güvenlik başlıkları.
 * CSP, mevcut inline script/style ve Filament/Livewire ile uyumlu tutulur.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set(
            'Permissions-Policy',
            'camera=(), microphone=(), geolocation=(), payment=(), usb=()'
        );

        if ($request->secure() || str_starts_with((string) config('app.url'), 'https://')) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains'
            );
        }

        if (! $response->headers->has('Content-Security-Policy')) {
            $response->headers->set('Content-Security-Policy', $this->contentSecurityPolicy());
        }

        return $response;
    }

    private function contentSecurityPolicy(): string
    {
        $directives = [
            "default-src 'self'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'self'",
            "object-src 'none'",
            "img-src 'self' data: blob: https:",
            "font-src 'self' data: https://fonts.bunny.net",
            "style-src 'self' 'unsafe-inline' https://fonts.bunny.net",
            // Inline store scripts + Filament/Livewire (Alpine) için unsafe-inline/eval
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://www.googletagmanager.com https://www.google-analytics.com",
            "connect-src 'self' https://www.google-analytics.com https://www.googletagmanager.com https://region1.google-analytics.com https://stats.g.doubleclick.net",
            "frame-src 'self' https://www.google.com https://maps.google.com",
        ];

        return implode('; ', $directives);
    }
}
