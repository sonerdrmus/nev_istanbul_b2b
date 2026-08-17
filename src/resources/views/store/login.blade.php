@extends('store.layout')

@section('title', __('store.login_page.title'))

@section('full_bleed')
    <section class="relative overflow-hidden">
        <div class="pointer-events-none absolute inset-0" aria-hidden="true">
            <div class="absolute left-1/2 top-0 h-72 w-[42rem] -translate-x-1/2 rounded-full bg-primary-100/70 blur-3xl"></div>
            <div class="absolute bottom-0 right-0 h-64 w-64 rounded-full bg-sky-100/80 blur-3xl"></div>
        </div>

        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
            <div class="grid items-stretch gap-6 lg:grid-cols-2 lg:gap-10">
                @include('store.partials.auth-aside', [
                    'kicker' => __('store.login_page.kicker'),
                    'title' => __('store.login_page.title'),
                    'lead' => __('store.login_page.lead'),
                    'highlights' => [
                        ['title' => __('store.login_page.benefit_prices'), 'text' => __('store.login_page.benefit_prices_text')],
                        ['title' => __('store.login_page.benefit_orders'), 'text' => __('store.login_page.benefit_orders_text')],
                        ['title' => __('store.login_page.benefit_company'), 'text' => __('store.login_page.benefit_company_text')],
                    ],
                ])

                <div class="relative flex">
                    <div class="relative flex w-full flex-col justify-center overflow-hidden rounded-3xl border border-slate-200/80 bg-white/90 p-6 shadow-[0_24px_60px_-32px_rgba(15,23,42,0.35)] backdrop-blur-md sm:p-8 lg:p-10">
                        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-primary-500 via-primary-400 to-sky-400" aria-hidden="true"></div>

                        <div class="flex items-start gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-primary-50 text-primary-700 ring-1 ring-primary-100">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-semibold tracking-tight text-slate-900">{{ __('store.login_modal.title') }}</h2>
                                <p class="mt-1 text-sm text-slate-500">{{ __('store.login_modal.subtitle') }}</p>
                            </div>
                        </div>

                        <form action="{{ route('store.login') }}" method="POST" class="mt-8">
                            @csrf
                            @if($errors->any())
                                <div class="mb-5 rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700">
                                    {{ $errors->first() }}
                                </div>
                            @endif

                            <div class="space-y-4">
                                <div>
                                    <label for="page-login-email" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('store.login_modal.email') }}</label>
                                    <div class="relative">
                                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        </span>
                                        <input id="page-login-email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email"
                                            placeholder="{{ __('store.login_page.email_placeholder') }}"
                                            class="w-full rounded-2xl border border-slate-200 bg-slate-50/80 py-3.5 pl-11 pr-4 text-[15px] text-slate-900 placeholder:text-slate-400 outline-none transition focus:border-primary-400 focus:bg-white focus:ring-4 focus:ring-primary-100 @error('email') border-red-400 @enderror">
                                    </div>
                                </div>

                                <div>
                                    <label for="page-login-password" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('store.login_modal.password') }}</label>
                                    <div class="relative">
                                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                        </span>
                                        <input id="page-login-password" name="password" type="password" required autocomplete="current-password"
                                            placeholder="{{ __('store.login_page.password_placeholder') }}"
                                            class="w-full rounded-2xl border border-slate-200 bg-slate-50/80 py-3.5 pl-11 pr-12 text-[15px] text-slate-900 placeholder:text-slate-400 outline-none transition focus:border-primary-400 focus:bg-white focus:ring-4 focus:ring-primary-100">
                                        <button type="button" id="page-login-password-toggle" class="absolute inset-y-0 right-0 flex items-center px-3.5 text-slate-400 hover:text-slate-700 transition-colors"
                                            aria-label="{{ __('store.login_page.show_password') }}" data-show="{{ __('store.login_page.show_password') }}" data-hide="{{ __('store.login_page.hide_password') }}">
                                            <svg id="page-login-eye" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </button>
                                    </div>
                                </div>

                                <label class="flex items-center gap-2.5 cursor-pointer select-none">
                                    <input name="remember" type="checkbox" value="1" class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                                    <span class="text-sm text-slate-600">{{ __('store.login_modal.remember') }}</span>
                                </label>
                            </div>

                            <button type="submit" class="mt-7 inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-primary-600 px-4 py-3.5 text-sm font-semibold text-white shadow-lg shadow-primary-600/20 transition hover:bg-primary-700 hover:shadow-primary-600/30">
                                {{ __('store.login_modal.submit') }}
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            </button>
                        </form>

                        <div class="mt-7 flex items-center gap-3 text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400">
                            <span class="h-px flex-1 bg-slate-200"></span>
                            {{ __('store.login_page.or') }}
                            <span class="h-px flex-1 bg-slate-200"></span>
                        </div>

                        <a href="{{ route('store.dealer-registration') }}" class="group mt-5 flex items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-slate-50/80 px-4 py-4 transition hover:border-primary-200 hover:bg-primary-50/60">
                            <span>
                                <span class="block text-[11px] font-semibold uppercase tracking-wide text-slate-400">{{ __('store.login_page.no_account') }}</span>
                                <span class="mt-0.5 block text-sm font-semibold text-slate-900 group-hover:text-primary-800">{{ __('store.login_page.register_title') }}</span>
                                <span class="mt-0.5 block text-xs text-slate-500">{{ __('store.login_page.register_hint') }}</span>
                            </span>
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-primary-600 shadow-sm ring-1 ring-slate-200 transition group-hover:translate-x-0.5">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        (function () {
            var input = document.getElementById('page-login-password');
            var btn = document.getElementById('page-login-password-toggle');
            if (!input || !btn) return;
            btn.addEventListener('click', function () {
                var hidden = input.type === 'password';
                input.type = hidden ? 'text' : 'password';
                btn.setAttribute('aria-label', hidden ? btn.getAttribute('data-hide') : btn.getAttribute('data-show'));
            });
        })();
    </script>
@endpush
