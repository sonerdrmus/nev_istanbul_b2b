<?php

namespace App\Http\Controllers;

use App\Support\SimpleTotp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminTwoFactorController extends Controller
{
    public function showSetup(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        abort_unless($user && $user->is_admin, 403);

        if ($user->hasTwoFactorEnabled() && $request->session()->get('admin_2fa_passed')) {
            return redirect(filament()->getPanel('admin')->getUrl());
        }

        $secret = $request->session()->get('admin_2fa_setup_secret');
        if (! is_string($secret) || $secret === '') {
            $secret = SimpleTotp::generateSecret();
            $request->session()->put('admin_2fa_setup_secret', $secret);
        }

        return view('admin.two-factor-setup', [
            'secret' => $secret,
            'otpAuthUrl' => SimpleTotp::otpAuthUrl($secret, (string) $user->email),
        ]);
    }

    public function confirmSetup(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user && $user->is_admin, 403);

        $validated = $request->validate([
            'code' => ['required', 'string'],
        ]);

        $secret = $request->session()->get('admin_2fa_setup_secret');
        if (! is_string($secret) || $secret === '' || ! SimpleTotp::verify($secret, $validated['code'])) {
            return back()->withErrors(['code' => 'Doğrulama kodu geçersiz.']);
        }

        $user->forceFill([
            'two_factor_secret' => encrypt($secret),
            'two_factor_confirmed_at' => now(),
        ])->save();

        $request->session()->forget('admin_2fa_setup_secret');
        $request->session()->put('admin_2fa_passed', true);

        return redirect(filament()->getPanel('admin')->getUrl())
            ->with('status', 'İki adımlı doğrulama etkinleştirildi.');
    }

    public function showChallenge(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        abort_unless($user && $user->is_admin, 403);

        if (! $user->hasTwoFactorEnabled()) {
            return redirect()->route('admin.2fa.setup');
        }

        if ($request->session()->get('admin_2fa_passed')) {
            return redirect(filament()->getPanel('admin')->getUrl());
        }

        return view('admin.two-factor-challenge');
    }

    public function confirmChallenge(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user && $user->is_admin, 403);

        $validated = $request->validate([
            'code' => ['required', 'string'],
        ]);

        $secret = $user->plainTwoFactorSecret();
        if ($secret === null || ! SimpleTotp::verify($secret, $validated['code'])) {
            return back()->withErrors(['code' => 'Doğrulama kodu geçersiz.']);
        }

        $request->session()->put('admin_2fa_passed', true);

        return redirect(filament()->getPanel('admin')->getUrl());
    }
}
