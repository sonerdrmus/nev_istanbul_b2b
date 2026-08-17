<?php

namespace App\Http\Controllers;

use App\Models\DealerRequest;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class StoreAccountController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectAfterLogin(Auth::user());
        }

        return view('store.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $email = (string) $request->input('email');
        $password = (string) $request->input('password');
        $user = \App\Models\User::query()->where('email', $email)->first();

        $fromLoginPage = str_contains((string) $request->headers->get('referer'), '/giris');

        if ($user && Hash::check($password, $user->password)) {
            if (! $user->is_admin && ! $user->is_approved) {
                return back()
                    ->withInput($request->only('email'))
                    ->withErrors(['email' => __('store.login_pending')])
                    ->with('open_login_modal', ! $fromLoginPage);
            }

            Auth::guard('web')->login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            return $this->redirectAfterLogin($user)->with('success', __('store.flash.login_ok'));
        }

        $pending = DealerRequest::query()
            ->where('email', $email)
            ->where('status', 'pending')
            ->first();

        if ($pending && filled($pending->password) && Hash::check($password, $pending->password)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => __('store.login_pending')])
                ->with('open_login_modal', ! $fromLoginPage);
        }

        return back()
            ->withErrors(['email' => __('store.login_failed')])
            ->withInput($request->only('email'))
            ->with('open_login_modal', ! $fromLoginPage);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', __('store.flash.logout_ok'));
    }

    public function account(): View|RedirectResponse
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('store.login.show');
        }

        if ($user->is_admin) {
            return redirect('/admin');
        }

        $user->load('company');

        $orders = Order::query()
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id);
                if (filled($user->email)) {
                    $query->orWhere('customer_email', $user->email);
                }
            })
            ->withCount('items')
            ->latest()
            ->get();

        return view('store.account', compact('user', 'orders'));
    }

    private function redirectAfterLogin(\App\Models\User $user): RedirectResponse
    {
        if ($user->is_admin) {
            return redirect('/admin');
        }

        return redirect()->intended(route('store.account'));
    }
}
