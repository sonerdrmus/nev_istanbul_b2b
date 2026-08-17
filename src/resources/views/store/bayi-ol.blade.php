@extends('store.layout')

@section('title', __('store.titles.dealer_register'))
@section('compact_chrome', '1')

@section('full_bleed')
    @php
        $businessProfiles = collect(\App\Models\DealerRequest::BUSINESS_PROFILES)
            ->mapWithKeys(fn (string $key) => [$key => __('store.dealer.business_profile.' . $key)])
            ->all();
        $interestAreas = collect(\App\Models\DealerRequest::INTEREST_AREA_KEYS)
            ->mapWithKeys(fn (string $key) => [$key => __('store.dealer.interest.' . $key)])
            ->all();
        $oldInterests = old('interest_areas', []);
        $showDelivery = (string) old('different_delivery_address', '') === '1';

        $wizardSteps = [
            1 => ['first_name', 'last_name', 'email', 'password', 'password_confirmation', 'phone', 'mobile_phone'],
            2 => [
                'business_name',
                'address_line_1',
                'address_line_2',
                'city',
                'postcode',
                'country',
                'different_delivery_address',
                'delivery_address_line_1',
                'delivery_address_line_2',
                'delivery_city',
                'delivery_postcode',
                'delivery_country',
            ],
            3 => [
                'business_type',
                'limited_company_name',
                'company_registration_number',
                'vat_reg_number',
                'website',
                'facebook',
                'instagram',
                'twitter',
                'linkedin',
            ],
            4 => ['business_profile', 'interest_areas', 'how_heard_about_us'],
            5 => ['terms_accepted'],
        ];

        $wizardStepTitles = [
            1 => __('store.dealer.steps.1'),
            2 => __('store.dealer.steps.2'),
            3 => __('store.dealer.steps.3'),
            4 => __('store.dealer.steps.4'),
            5 => __('store.dealer.steps.5'),
        ];

        $initialWizardStep = 1;
        if ($errors->any()) {
            foreach ($wizardSteps as $num => $keys) {
                foreach ($keys as $key) {
                    if ($errors->has($key) || $errors->has($key.'.*')) {
                        $initialWizardStep = max($initialWizardStep, (int) $num);
                    }
                }
            }
        }

        $inputClass = 'w-full rounded-2xl border border-slate-200 bg-slate-50/80 px-4 py-3.5 text-[15px] text-slate-900 placeholder:text-slate-400 outline-none transition focus:border-primary-400 focus:bg-white focus:ring-4 focus:ring-primary-100';
        $labelClass = 'mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500';
        $chipClass = 'inline-flex cursor-pointer items-center rounded-2xl border border-slate-200 bg-white px-3.5 py-2 text-sm text-slate-700 shadow-sm transition hover:border-primary-200 hover:bg-primary-50/60 has-[:checked]:border-primary-600 has-[:checked]:bg-primary-600 has-[:checked]:text-white has-[:checked]:shadow-none';
    @endphp

    <style>
        .dealer-step-pane.hidden { display: none !important; }
        details.dealer-extra summary::-webkit-details-marker { display: none; }
        .wiz-rail [data-step-indicator].wiz-upcoming { border-color: rgba(255,255,255,0.18); background: rgba(255,255,255,0.06); color: rgba(255,255,255,0.55); }
        .wiz-rail [data-step-indicator].wiz-current { border-color: #fff; background: #fff; color: #114a8c; box-shadow: 0 12px 24px -12px rgba(255,255,255,0.65); }
        .wiz-rail [data-step-indicator].wiz-done { border-color: rgb(56 189 248 / 0.5); background: rgb(14 165 233); color: #fff; }
        .wiz-rail [data-step-caption].wiz-cap-current { color: #fff; font-weight: 600; }
        .wiz-rail [data-step-caption].wiz-cap-done { color: rgb(186 230 253); }
        .wiz-rail [data-step-caption].wiz-cap-todo { color: rgba(255,255,255,0.45); }
        .wiz-mobile [data-step-indicator].wiz-upcoming { border-color: rgb(226 232 240); background: #fff; color: rgb(148 163 184); }
        .wiz-mobile [data-step-indicator].wiz-current { border-color: rgb(21 96 179); background: rgb(21 96 179); color: #fff; box-shadow: 0 10px 20px -12px rgb(21 96 179 / 0.9); }
        .wiz-mobile [data-step-indicator].wiz-done { border-color: rgb(125 211 252); background: rgb(240 249 255); color: rgb(3 105 161); }
        .wiz-mobile [data-step-caption].wiz-cap-current { color: rgb(15 23 42); font-weight: 600; }
        .wiz-mobile [data-step-caption].wiz-cap-done { color: rgb(14 116 144); }
        .wiz-mobile [data-step-caption].wiz-cap-todo { color: rgb(148 163 184); }
    </style>

    <section class="relative flex h-full min-h-0 flex-col overflow-hidden">
        <div class="pointer-events-none absolute inset-0" aria-hidden="true">
            <div class="absolute left-1/3 top-0 h-72 w-[36rem] -translate-x-1/2 rounded-full bg-primary-100/70 blur-3xl"></div>
            <div class="absolute bottom-0 right-0 h-64 w-64 rounded-full bg-sky-100/70 blur-3xl"></div>
        </div>

        <div class="relative mx-auto grid h-full min-h-0 w-full max-w-7xl grid-cols-1 gap-4 px-3 py-3 sm:px-5 lg:grid-cols-12 lg:gap-6 lg:px-8 lg:py-5">
            <aside class="relative hidden min-h-0 overflow-hidden rounded-3xl bg-slate-950 text-white shadow-[0_24px_60px_-32px_rgba(15,23,42,0.55)] lg:col-span-4 lg:flex">
                <div class="pointer-events-none absolute inset-0" aria-hidden="true">
                    <div class="absolute -left-16 -top-16 h-56 w-56 rounded-full bg-primary-500/35 blur-3xl"></div>
                    <div class="absolute right-[-2rem] bottom-10 h-64 w-64 rounded-full bg-sky-400/20 blur-3xl"></div>
                    <div class="absolute inset-0 opacity-[0.16]" style="background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.5) 1px, transparent 0); background-size: 22px 22px;"></div>
                </div>
                <div class="relative flex h-full min-h-0 w-full flex-col px-7 py-8">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-primary-300">{{ __('store.dealer.panel_kicker') }}</p>
                    <h1 class="mt-3 text-2xl font-semibold tracking-tight">{{ __('store.dealer.application_title') }}</h1>
                    <p class="mt-2 text-sm leading-relaxed text-slate-300">{{ __('store.dealer.intro_short') }}</p>
                    <div class="mt-8 min-h-0 flex-1 overflow-y-auto pr-1">
                        @include('store.partials.dealer-wizard-stepper', ['variant' => 'rail', 'wizardStepTitles' => $wizardStepTitles])
                    </div>
                    <p class="mt-6 text-sm text-slate-400">
                        {{ __('store.dealer.already_account') }}
                        <a href="{{ route('store.login.show') }}" class="font-semibold text-white underline decoration-primary-400/80 underline-offset-4 hover:text-primary-200">{{ __('store.dealer.sign_in_link') }}</a>
                    </p>
                </div>
            </aside>

            <div class="flex min-h-0 flex-col lg:col-span-8">
                <div class="relative flex min-h-0 flex-1 flex-col overflow-hidden rounded-3xl border border-slate-200/80 bg-white/95 shadow-[0_24px_60px_-32px_rgba(15,23,42,0.35)] backdrop-blur">
                    <div class="h-1.5 bg-slate-100">
                        <div id="dealer-wizard-progress" class="h-full bg-gradient-to-r from-primary-600 to-sky-400 transition-[width] duration-300" style="width: {{ ($initialWizardStep / 5) * 100 }}%"></div>
                    </div>

            <form id="dealer-wizard-form" action="{{ route('dealer-requests.store') }}" method="POST" class="flex min-h-0 flex-1 flex-col" novalidate>
                @csrf

                @if($errors->any())
                    <div class="mx-5 mt-5 shrink-0 rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700 sm:mx-8">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div class="shrink-0 border-b border-slate-100 px-5 py-4 lg:hidden">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-primary-600">{{ __('store.dealer.panel_kicker') }}</p>
                    <h1 class="mt-1 text-lg font-semibold tracking-tight text-slate-900">{{ __('store.dealer.application_title') }}</h1>
                    <div class="mt-4">
                        @include('store.partials.dealer-wizard-stepper', ['variant' => 'mobile', 'wizardStepTitles' => $wizardStepTitles])
                    </div>
                </div>

                <div class="shrink-0 px-5 pt-5 sm:px-8 lg:px-10 lg:pt-7">
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">
                        <span id="dealer-wizard-step-label">{{ $initialWizardStep }} / 5</span>
                    </p>
                    <h2 id="dealer-wizard-current-title" class="mt-1 text-xl font-semibold tracking-tight text-slate-900 sm:text-2xl">{{ $wizardStepTitles[$initialWizardStep] }}</h2>
                </div>

                <div class="flex min-h-0 flex-1 flex-col overflow-y-auto px-5 py-5 sm:px-8 lg:px-10">
                    {{-- Adım 1 --}}
                    <div data-step="1" class="dealer-step-pane mx-auto w-full max-w-2xl space-y-5 @unless($initialWizardStep === 1) hidden @endunless">
                        <div class="grid grid-cols-1 gap-x-4 gap-y-3 sm:grid-cols-2">
                            <div>
                                <label for="first_name" class="{{ $labelClass }}">{{ __('store.dealer.first_name') }} *</label>
                                <input id="first_name" name="first_name" type="text" value="{{ old('first_name') }}" required autocomplete="given-name" class="{{ $inputClass }} @error('first_name') border-red-400 @enderror">
                            </div>
                            <div>
                                <label for="last_name" class="{{ $labelClass }}">{{ __('store.dealer.last_name') }} *</label>
                                <input id="last_name" name="last_name" type="text" value="{{ old('last_name') }}" required autocomplete="family-name" class="{{ $inputClass }} @error('last_name') border-red-400 @enderror">
                            </div>
                            <div class="sm:col-span-2">
                                <label for="email" class="{{ $labelClass }}">{{ __('store.dealer.email') }} *</label>
                                <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email" class="{{ $inputClass }} @error('email') border-red-400 @enderror">
                            </div>
                            <div>
                                <label for="password" class="{{ $labelClass }}">{{ __('store.dealer.password') }} *</label>
                                <div class="relative">
                                    <input id="password" name="password" type="password" required autocomplete="new-password" class="{{ $inputClass }} pr-10 @error('password') border-red-400 @enderror">
                                    <button type="button" class="js-password-toggle absolute inset-y-0 right-0 px-3 text-slate-400 hover:text-slate-700" data-target="password" aria-label="{{ __('store.login_page.show_password') }}">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>
                                </div>
                                <p class="mt-1.5 text-xs text-slate-500">{{ __('store.dealer.password_hint') }}</p>
                            </div>
                            <div>
                                <label for="password_confirmation" class="{{ $labelClass }}">{{ __('store.dealer.password_confirmation') }} *</label>
                                <div class="relative">
                                    <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="{{ $inputClass }} pr-10">
                                    <button type="button" class="js-password-toggle absolute inset-y-0 right-0 px-3 text-slate-400 hover:text-slate-700" data-target="password_confirmation" aria-label="{{ __('store.login_page.show_password') }}">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label for="phone" class="{{ $labelClass }}">{{ __('store.dealer.phone') }} *</label>
                                <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" required autocomplete="tel" class="{{ $inputClass }} @error('phone') border-red-400 @enderror">
                            </div>
                            <div>
                                <label for="mobile_phone" class="{{ $labelClass }}">{{ __('store.dealer.mobile') }}</label>
                                <input id="mobile_phone" name="mobile_phone" type="tel" value="{{ old('mobile_phone') }}" autocomplete="tel-national" class="{{ $inputClass }}">
                            </div>
                        </div>
                    </div>

                    {{-- Adım 2 --}}
                    <div data-step="2" class="dealer-step-pane mx-auto w-full max-w-2xl space-y-5 @unless($initialWizardStep === 2) hidden @endunless">
                        <p class="text-sm text-slate-500">{{ __('store.dealer.address_note') }}</p>
                        <div class="grid grid-cols-1 gap-x-4 gap-y-3 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label for="business_name" class="{{ $labelClass }}">{{ __('store.dealer.business_name') }} *</label>
                                <input id="business_name" name="business_name" type="text" value="{{ old('business_name') }}" required autocomplete="organization" class="{{ $inputClass }} @error('business_name') border-red-400 @enderror">
                            </div>
                            <div class="sm:col-span-2">
                                <label for="address_line_1" class="{{ $labelClass }}">{{ __('store.dealer.address_1') }} *</label>
                                <input id="address_line_1" name="address_line_1" type="text" value="{{ old('address_line_1') }}" required autocomplete="address-line1" class="{{ $inputClass }} @error('address_line_1') border-red-400 @enderror">
                            </div>
                            <div class="sm:col-span-2">
                                <label for="address_line_2" class="{{ $labelClass }}">{{ __('store.dealer.address_2') }}</label>
                                <input id="address_line_2" name="address_line_2" type="text" value="{{ old('address_line_2') }}" autocomplete="address-line2" class="{{ $inputClass }}">
                            </div>
                            <div>
                                <label for="city" class="{{ $labelClass }}">{{ __('store.dealer.city') }} *</label>
                                <input id="city" name="city" type="text" value="{{ old('city') }}" required autocomplete="address-level2" class="{{ $inputClass }} @error('city') border-red-400 @enderror">
                            </div>
                            <div>
                                <label for="postcode" class="{{ $labelClass }}">{{ __('store.dealer.postcode') }} *</label>
                                <input id="postcode" name="postcode" type="text" value="{{ old('postcode') }}" required autocomplete="postal-code" class="{{ $inputClass }} @error('postcode') border-red-400 @enderror">
                            </div>
                            <div class="sm:col-span-2">
                                <label for="country" class="{{ $labelClass }}">{{ __('store.dealer.country') }} *</label>
                                <input id="country" name="country" type="text" value="{{ old('country') }}" required autocomplete="country-name" class="{{ $inputClass }} @error('country') border-red-400 @enderror">
                            </div>
                        </div>
                        <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm text-slate-700">
                            <input id="different_delivery_address" type="checkbox" name="different_delivery_address" value="1" class="mt-0.5 h-4 w-4 rounded border-slate-300 text-primary-600" @checked($showDelivery)>
                            <span>{{ __('store.dealer.different_delivery') }}</span>
                        </label>
                        <div id="dealer-delivery-fields" class="grid grid-cols-1 gap-x-4 gap-y-3 rounded-xl border border-slate-200 p-3 sm:grid-cols-2 {{ $showDelivery ? '' : 'hidden' }}">
                            <div class="sm:col-span-2">
                                <label for="delivery_address_line_1" class="{{ $labelClass }}">{{ __('store.dealer.delivery_address_1') }} *</label>
                                <input id="delivery_address_line_1" name="delivery_address_line_1" type="text" value="{{ old('delivery_address_line_1') }}" autocomplete="shipping address-line1" class="{{ $inputClass }}" @unless($showDelivery) disabled @endunless @if($showDelivery) required @endif>
                            </div>
                            <input id="delivery_address_line_2" name="delivery_address_line_2" type="text" value="{{ old('delivery_address_line_2') }}" class="hidden" @unless($showDelivery) disabled @endunless>
                            <div>
                                <label for="delivery_city" class="{{ $labelClass }}">{{ __('store.dealer.delivery_city') }} *</label>
                                <input id="delivery_city" name="delivery_city" type="text" value="{{ old('delivery_city') }}" autocomplete="shipping address-level2" class="{{ $inputClass }}" @unless($showDelivery) disabled @endunless @if($showDelivery) required @endif>
                            </div>
                            <div>
                                <label for="delivery_postcode" class="{{ $labelClass }}">{{ __('store.dealer.delivery_postcode') }} *</label>
                                <input id="delivery_postcode" name="delivery_postcode" type="text" value="{{ old('delivery_postcode') }}" autocomplete="shipping postal-code" class="{{ $inputClass }}" @unless($showDelivery) disabled @endunless @if($showDelivery) required @endif>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="delivery_country" class="{{ $labelClass }}">{{ __('store.dealer.delivery_country') }} *</label>
                                <input id="delivery_country" name="delivery_country" type="text" value="{{ old('delivery_country') }}" autocomplete="shipping country-name" class="{{ $inputClass }}" @unless($showDelivery) disabled @endunless @if($showDelivery) required @endif>
                            </div>
                        </div>
                    </div>

                    {{-- Adım 3 --}}
                    <div data-step="3" class="dealer-step-pane mx-auto w-full max-w-2xl space-y-5 @unless($initialWizardStep === 3) hidden @endunless">
                        <p class="text-sm text-slate-500">{{ __('store.dealer.business_note') }}</p>
                        <div>
                            <label for="business_type" class="{{ $labelClass }}">{{ __('store.dealer.business_type') }} *</label>
                            <input id="business_type" name="business_type" type="text" value="{{ old('business_type') }}" required class="{{ $inputClass }} @error('business_type') border-red-400 @enderror">
                        </div>
                        <details class="dealer-extra group rounded-2xl border border-slate-200">
                            <summary class="flex cursor-pointer list-none items-center justify-between px-4 py-3.5 text-sm text-slate-700 hover:bg-slate-50">
                                <span class="font-medium">{{ __('store.dealer.optional_details') }}</span>
                                <span class="text-[11px] text-slate-400">{{ __('store.dealer.optional_hint') }}</span>
                            </summary>
                            <div class="grid grid-cols-1 gap-3 border-t border-slate-100 p-3 sm:grid-cols-2">
                                <input name="limited_company_name" type="text" value="{{ old('limited_company_name') }}" placeholder="{{ __('store.dealer.limited_name') }}" class="{{ $inputClass }} sm:col-span-2">
                                <input name="company_registration_number" type="text" value="{{ old('company_registration_number') }}" placeholder="{{ __('store.dealer.reg_number') }}" class="{{ $inputClass }}">
                                <input name="vat_reg_number" type="text" value="{{ old('vat_reg_number') }}" placeholder="{{ __('store.dealer.vat') }}" class="{{ $inputClass }}">
                                <input name="website" type="text" value="{{ old('website') }}" placeholder="{{ __('store.dealer.website_placeholder') }}" class="{{ $inputClass }} sm:col-span-2">
                                <input name="facebook" type="text" value="{{ old('facebook') }}" placeholder="{{ __('store.dealer.social_facebook') }}" class="{{ $inputClass }}">
                                <input name="instagram" type="text" value="{{ old('instagram') }}" placeholder="{{ __('store.dealer.social_instagram') }}" class="{{ $inputClass }}">
                                <input name="twitter" type="text" value="{{ old('twitter') }}" placeholder="{{ __('store.dealer.social_twitter') }}" class="{{ $inputClass }}">
                                <input name="linkedin" type="text" value="{{ old('linkedin') }}" placeholder="{{ __('store.dealer.social_linkedin') }}" class="{{ $inputClass }}">
                            </div>
                        </details>
                    </div>

                    {{-- Adım 4 --}}
                    <div data-step="4" class="dealer-step-pane mx-auto w-full max-w-2xl space-y-5 @unless($initialWizardStep === 4) hidden @endunless">
                        <fieldset>
                            <legend class="{{ $labelClass }}">{{ __('store.dealer.profile_question') }} *</legend>
                            <div class="flex flex-wrap gap-2">
                                @foreach($businessProfiles as $value => $label)
                                    <label class="{{ $chipClass }}">
                                        <input type="radio" name="business_profile" value="{{ $value }}" class="sr-only" @checked(old('business_profile') === $value)>
                                        {{ $label }}
                                    </label>
                                @endforeach
                            </div>
                        </fieldset>
                        <fieldset>
                            <legend class="{{ $labelClass }}">{{ __('store.dealer.interests') }} *</legend>
                            <div class="flex flex-wrap gap-2">
                                @foreach($interestAreas as $value => $label)
                                    <label class="{{ $chipClass }}">
                                        <input type="checkbox" name="interest_areas[]" value="{{ $value }}" class="sr-only" @checked(in_array($value, $oldInterests, true))>
                                        {{ $label }}
                                    </label>
                                @endforeach
                            </div>
                        </fieldset>
                        <div>
                            <label for="how_heard_about_us" class="{{ $labelClass }}">{{ __('store.dealer.heard') }} *</label>
                            <input id="how_heard_about_us" name="how_heard_about_us" type="text" value="{{ old('how_heard_about_us') }}" required class="{{ $inputClass }} @error('how_heard_about_us') border-red-400 @enderror">
                        </div>
                    </div>

                    {{-- Adım 5 --}}
                    <div data-step="5" class="dealer-step-pane mx-auto w-full max-w-2xl space-y-5 @unless($initialWizardStep === 5) hidden @endunless">
                        <p class="text-sm text-slate-500">{{ __('store.dealer.terms_intro') }}</p>
                        <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4 text-[15px] text-slate-700">
                            <input type="checkbox" name="terms_accepted" value="1" required class="mt-1 h-4 w-4 rounded border-slate-300 text-primary-600" @checked(old('terms_accepted'))>
                            <span>{!! __('store.dealer.terms_accept', ['url' => route('store.legal.show', \App\Models\LegalPage::TERMS_SLUG)]) !!}</span>
                        </label>
                        <p class="text-sm leading-relaxed text-slate-500">
                            {!! __('store.dealer.terms_note', ['url' => route('store.legal.show', \App\Models\LegalPage::PRIVACY_SLUG)]) !!}
                        </p>
                    </div>
                </div>

                <div class="flex shrink-0 flex-col-reverse gap-3 border-t border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:px-8 lg:px-10">
                    <a href="{{ route('home') }}" class="text-center text-sm font-medium text-slate-500 hover:text-slate-800 sm:text-left">{{ __('store.dealer.cancel') }}</a>
                    <div class="hidden sm:flex sm:flex-1"></div>
                    <div class="flex gap-2">
                        <button type="button" id="dealer-wizard-prev" class="hidden flex-1 rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50 sm:flex-none">
                            {{ __('store.dealer.back') }}
                        </button>
                        <button type="button" id="dealer-wizard-next" class="inline-flex flex-1 items-center justify-center gap-2 rounded-2xl bg-primary-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-primary-600/20 hover:bg-primary-700 sm:flex-none">
                            {{ __('store.dealer.next') }}
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </button>
                        <button type="submit" id="dealer-wizard-submit" class="hidden inline-flex flex-1 items-center justify-center gap-2 rounded-2xl bg-primary-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-primary-600/20 hover:bg-primary-700 sm:flex-none">
                            {{ __('store.dealer.submit') }}
                        </button>
                    </div>
                </div>
            </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        (function () {
            document.querySelectorAll('.js-password-toggle').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var input = document.getElementById(btn.getAttribute('data-target'));
                    if (!input) return;
                    input.type = input.type === 'password' ? 'text' : 'password';
                });
            });

            var form = document.getElementById('dealer-wizard-form');
            if (!form) return;
            var TOTAL = 5;
            var STEP_TITLES = @json(array_values($wizardStepTitles));
            var WIZ_I18N = {
                validationProfile: @json(__('store.dealer.validation_select_profile')),
                validationInterest: @json(__('store.dealer.validation_interests'))
            };
            var initial = {{ (int) $initialWizardStep }};
            if (initial < 1 || initial > TOTAL) initial = 1;

            var panes = form.querySelectorAll('.dealer-step-pane');
            var stepLabel = document.getElementById('dealer-wizard-step-label');
            var currentTitle = document.getElementById('dealer-wizard-current-title');
            var progress = document.getElementById('dealer-wizard-progress');
            var btnPrev = document.getElementById('dealer-wizard-prev');
            var btnNext = document.getElementById('dealer-wizard-next');
            var btnSubmit = document.getElementById('dealer-wizard-submit');

            function paneFor(step) {
                return form.querySelector('.dealer-step-pane[data-step="' + step + '"]');
            }

            function getInputs(step) {
                var p = paneFor(step);
                if (!p) return [];
                return Array.prototype.slice.call(p.querySelectorAll('input, select, textarea')).filter(function (el) {
                    if (el.type === 'hidden' || el.disabled || el.classList.contains('sr-only') && el.type !== 'radio' && el.type !== 'checkbox') return false;
                    if (el.closest('details') && !el.closest('details').open) return false;
                    var checkName = el.getAttribute('name');
                    return checkName && checkName.indexOf('_method') !== 0;
                });
            }

            function validatePane(step) {
                var inputs = getInputs(step);
                for (var i = 0; i < inputs.length; i++) {
                    if (inputs[i].classList.contains('sr-only')) continue;
                    if (!inputs[i].reportValidity()) {
                        inputs[i].focus({ preventScroll: false });
                        return false;
                    }
                }
                if (step === 4) {
                    var checkedProfiles = form.querySelectorAll('input[name="business_profile"]:checked');
                    if (!checkedProfiles.length) {
                        var firstRadio = form.querySelector('input[name="business_profile"]');
                        if (firstRadio && firstRadio.setCustomValidity) {
                            firstRadio.setCustomValidity(WIZ_I18N.validationProfile);
                            firstRadio.reportValidity();
                            firstRadio.setCustomValidity('');
                        }
                        return false;
                    }
                    var interests = form.querySelectorAll('input[name="interest_areas[]"]:checked');
                    if (!interests.length) {
                        var cb = form.querySelector('input[name="interest_areas[]"]');
                        if (cb && cb.setCustomValidity) {
                            cb.setCustomValidity(WIZ_I18N.validationInterest);
                            cb.reportValidity();
                            cb.setCustomValidity('');
                        }
                        return false;
                    }
                }
                return true;
            }

            function updateStepIndicators(step) {
                document.querySelectorAll('[data-step-indicator]').forEach(function (el) {
                    var n = parseInt(el.getAttribute('data-step-indicator'), 10);
                    el.classList.remove('wiz-done', 'wiz-current', 'wiz-upcoming');
                    if (n < step) el.classList.add('wiz-done');
                    else if (n === step) el.classList.add('wiz-current');
                    else el.classList.add('wiz-upcoming');
                });
                document.querySelectorAll('[data-step-caption]').forEach(function (el) {
                    var n = parseInt(el.getAttribute('data-step-caption'), 10);
                    el.classList.remove('wiz-cap-current', 'wiz-cap-done', 'wiz-cap-todo');
                    if (n === step) el.classList.add('wiz-cap-current');
                    else if (n < step) el.classList.add('wiz-cap-done');
                    else el.classList.add('wiz-cap-todo');
                });
            }

            function showStep(step) {
                panes.forEach(function (pane) {
                    var n = parseInt(pane.getAttribute('data-step'), 10);
                    if (n === step) pane.classList.remove('hidden');
                    else pane.classList.add('hidden');
                });
                stepLabel.textContent = step + ' / ' + TOTAL;
                currentTitle.textContent = STEP_TITLES[step - 1] || '';
                if (progress) progress.style.width = ((step / TOTAL) * 100) + '%';
                btnPrev.classList.toggle('hidden', step <= 1);
                btnNext.classList.toggle('hidden', step >= TOTAL);
                btnSubmit.classList.toggle('hidden', step < TOTAL);
                updateStepIndicators(step);
                form.dataset.currentStep = String(step);
                var p = paneFor(step);
                if (p) {
                    var firstFocus = p.querySelector('input:not([type="hidden"]):not(.sr-only), textarea, select');
                    try { firstFocus && firstFocus.focus(); } catch (e) {}
                }
            }

            btnNext.addEventListener('click', function () {
                var current = parseInt(form.dataset.currentStep, 10) || 1;
                if (!validatePane(current)) return;
                showStep(Math.min(TOTAL, current + 1));
            });

            btnPrev.addEventListener('click', function () {
                var current = parseInt(form.dataset.currentStep, 10) || 1;
                showStep(Math.max(1, current - 1));
            });

            form.addEventListener('submit', function (e) {
                for (var s = 1; s <= TOTAL; s++) {
                    if (!validatePane(s)) {
                        e.preventDefault();
                        showStep(s);
                        return;
                    }
                }
            });

            showStep(initial);

            var deliveryToggle = document.getElementById('different_delivery_address');
            var deliveryFields = document.getElementById('dealer-delivery-fields');
            function syncDeliveryFields() {
                if (!deliveryToggle || !deliveryFields) return;
                var on = deliveryToggle.checked;
                deliveryFields.classList.toggle('hidden', !on);
                deliveryFields.querySelectorAll('input').forEach(function (el) {
                    el.disabled = !on;
                    if (el.id === 'delivery_address_line_2') return;
                    if (on) el.setAttribute('required', 'required');
                    else el.removeAttribute('required');
                });
            }
            if (deliveryToggle) {
                deliveryToggle.addEventListener('change', syncDeliveryFields);
                syncDeliveryFields();
            }
        })();
    </script>
@endpush
