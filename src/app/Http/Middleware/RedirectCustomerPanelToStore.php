<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectCustomerPanelToStore
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user?->is_admin) {
            return redirect('/admin');
        }

        if ($user) {
            return redirect()->route('store.account');
        }

        return redirect()->route('store.login.show');
    }
}
