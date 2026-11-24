<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictAdminByIp
{
    public function handle(Request $request, Closure $next): Response
    {
        $allowed = config('store.admin_allowed_ips', []);
        if (! is_array($allowed) || $allowed === []) {
            return $next($request);
        }

        $ip = (string) $request->ip();
        foreach ($allowed as $rule) {
            $rule = trim((string) $rule);
            if ($rule === '') {
                continue;
            }
            if ($this->matches($ip, $rule)) {
                return $next($request);
            }
        }

        abort(403, 'Bu IP adresinden yönetim paneline erişim yok.');
    }

    private function matches(string $ip, string $rule): bool
    {
        if (! str_contains($rule, '/')) {
            return hash_equals($rule, $ip);
        }

        [$subnet, $mask] = array_pad(explode('/', $rule, 2), 2, null);
        if ($subnet === null || $mask === null || ! is_numeric($mask)) {
            return false;
        }

        $ipLong = ip2long($ip);
        $subnetLong = ip2long($subnet);
        if ($ipLong === false || $subnetLong === false) {
            return false;
        }

        $mask = (int) $mask;
        if ($mask < 0 || $mask > 32) {
            return false;
        }

        $maskLong = $mask === 0 ? 0 : (~((1 << (32 - $mask)) - 1) & 0xFFFFFFFF);

        return ($ipLong & $maskLong) === ($subnetLong & $maskLong);
    }
}
