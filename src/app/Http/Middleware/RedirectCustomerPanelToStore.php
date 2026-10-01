<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectCustomerPanelToStore
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user?->is_admin) {
            return redirect(Filament::getPanel('admin')->getUrl());
        }

        if ($user) {
            return redirect()->route('store.account');
        }

        return redirect()->route('store.login.show');
    }
}
