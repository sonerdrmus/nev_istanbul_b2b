@extends('store.layout')

@section('title', __('store.titles.dealer_register'))

@section('content')
    <style>
        .dealer-step-pane.hidden { display: none !important; }
        details summary::-webkit-details-marker { display: none; }
        .wiz-track { background: rgb(241 245 249); }
        [data-step-indicator].wiz-done { border-color: rgb(226 232 240); background: rgb(248 250 252); color: rgb(71 85 105); font-weight: 500; }
        [data-step-indicator].wiz-current { border-color: rgb(21 96 179); background: rgb(255 255 255); color: rgb(21 96 179); font-weight: 600; }
        [data-step-indicator].wiz-upcoming { border-color: rgb(226 232 240); background: rgb(255 255 255); color: rgb(148 163 184); font-weight: 500; }
    </style>

    @php
        $businessProfiles = collect(\App\Models\DealerRequest::BUSINESS_PROFILES)
            ->mapWithKeys(fn (string $key) => [$key => __('store.dealer.business_profile.' . $key)])
            ->all();
        $interestAreas = collect(\App\Models\DealerRequest::INTEREST_AREA_KEYS)
            ->mapWithKeys(fn (string $key) => [$key => __('store.dealer.interest.' . $key)])
            ->all();
        $oldInterests = old('interest_areas', []);

        $wizardSteps = [
            1 => ['first_name', 'last_name', 'email', 'phone', 'mobile_phone'],
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

        $inputClass = 'w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-[15px] leading-normal text-slate-900 placeholder:text-slate-400 focus:border-slate-400 focus:ring-1 focus:ring-slate-300 transition-colors outline-none';
        $labelClass = 'block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1';
    @endphp

    <div class="w-full">
        <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-sm">
            <div class="px-6 sm:px-8 lg:px-12 py-8 border-b border-slate-100">
                <div class="max-w-3xl lg:max-w-full mx-auto lg:mx-0">
                    <h1 class="text-lg sm:text-xl font-medium text-slate-900 tracking-tight">{{ __('store.dealer.application_title') }}</h1>
                    <p class="text-sm text-slate-500 mt-1">{{ __('store.dealer.intro_short') }}</p>
                </div>

                {{-- İlerleme --}}
                <div class="max-w-3xl lg:max-w-none mx-auto mt-8 lg:mt-10">
                    <div class="flex justify-between items-center pb-5">
                        @foreach($wizardStepTitles as $stepNum => $_)
                            <div class="flex flex-1 justify-center min-w-0">
                                <div
                                    data-step-indicator="{{ $stepNum }}"
                                    class="wiz-upcoming flex h-9 w-9 sm:h-10 sm:w-10 shrink-0 items-center justify-center rounded-full border text-sm transition-colors duration-200"
                                >{{ $stepNum }}</div>
                            </div>
                        @endforeach
                    </div>
                    <div class="wiz-track h-2 rounded-full w-full overflow-hidden">
                        <div
                            id="dealer-wizard-progress"
                            class="h-full min-w-[4px] rounded-full bg-primary-600 transition-[width] duration-300 ease-out"
                            style="width: 20%"
                        ></div>
                    </div>
                    <div class="flex justify-between gap-x-1 sm:gap-2 pt-3">
                        @foreach($wizardStepTitles as $stepNum => $shortTitle)
                            <div class="flex-1 flex justify-center min-w-0">
                                <span data-step-caption="{{ $stepNum }}" class="block text-[10px] sm:text-[11px] font-medium text-center text-slate-400 leading-snug px-px line-clamp-2">{{ $shortTitle }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-6 pt-6 border-t border-slate-100 text-center lg:text-left space-y-1">
                        <p id="dealer-wizard-current-title" class="text-base sm:text-lg font-medium text-slate-900">{{ $wizardStepTitles[$initialWizardStep] }}</p>
                        <p id="dealer-wizard-next-hint-wrap" class="text-sm text-slate-500 mt-1">
                            <span class="tabular-nums text-slate-600" id="dealer-wizard-step-label">{{ $initialWizardStep }} / 5</span>
                            <span class="text-slate-300 mx-2" aria-hidden="true">·</span>
                            <span id="dealer-badge-next-role">{{ $initialWizardStep >= 5 ? __('store.dealer.complete_label') : __('store.dealer.next_label') }}</span>&#32;
                            <span id="dealer-wizard-next-preview" class="text-slate-700">
                                @if ($initialWizardStep >= 5)
                                    {{ __('store.dealer.complete_hint') }}
                                @else
                                    {{ $wizardStepTitles[$initialWizardStep + 1] }}
                                @endif
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <form id="dealer-wizard-form" action="{{ route('dealer-requests.store') }}" method="POST" class="px-6 sm:px-8 lg:px-12 xl:px-14 py-8 lg:py-10" novalidate>
                @csrf
                @if($errors->any())
                    <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 space-y-0.5">
                        @foreach($errors->all() as $err)
                            <p>{{ $err }}</p>
                        @endforeach
                    </div>
                @endif

                {{-- Alanlar grid: geniş ekranda 2 kolon --}}
                <div class="max-w-none">
                {{-- Adım 1 --}}
                <div data-step="1" class="space-y-6 dealer-step-pane @unless($initialWizardStep === 1) hidden @endunless">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-5">
                        <div>
                            <label for="first_name" class="{{ $labelClass }}">{{ __('store.dealer.first_name') }} <span class="text-red-500">*</span></label>
                            <input id="first_name" name="first_name" type="text" value="{{ old('first_name') }}" required autocomplete="given-name"
                                class="{{ $inputClass }} @error('first_name') border-red-400 @enderror">
                        </div>
                        <div>
                            <label for="last_name" class="{{ $labelClass }}">{{ __('store.dealer.last_name') }} <span class="text-red-500">*</span></label>
                            <input id="last_name" name="last_name" type="text" value="{{ old('last_name') }}" required autocomplete="family-name"
                                class="{{ $inputClass }} @error('last_name') border-red-400 @enderror">
                        </div>
                        <div class="md:col-span-2">
                            <label for="email" class="{{ $labelClass }}">{{ __('store.dealer.email') }} <span class="text-red-500">*</span></label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email"
                                class="{{ $inputClass }} @error('email') border-red-400 @enderror">
                        </div>
                        <div>
                            <label for="phone" class="{{ $labelClass }}">{{ __('store.dealer.phone') }} <span class="text-red-500">*</span></label>
                            <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" required autocomplete="tel"
                                class="{{ $inputClass }} @error('phone') border-red-400 @enderror">
                        </div>
                        <div>
                            <label for="mobile_phone" class="{{ $labelClass }}">{{ __('store.dealer.mobile') }}</label>
                            <input id="mobile_phone" name="mobile_phone" type="tel" value="{{ old('mobile_phone') }}" autocomplete="tel-national"
                                class="{{ $inputClass }} @error('mobile_phone') border-red-400 @enderror">
                        </div>
                    </div>
                </div>

                {{-- Adım 2 --}}
                <div data-step="2" class="space-y-5 dealer-step-pane @unless($initialWizardStep === 2) hidden @endunless">
                    <p class="text-sm text-slate-500 max-w-2xl">{{ __('store.dealer.address_note') }}</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-5">
                        <div class="md:col-span-2">
                            <label for="business_name" class="{{ $labelClass }}">{{ __('store.dealer.business_name') }} <span class="text-red-500">*</span></label>
                            <input id="business_name" name="business_name" type="text" value="{{ old('business_name') }}" required autocomplete="organization"
                                class="{{ $inputClass }} @error('business_name') border-red-400 @enderror">
                        </div>
                        <div class="md:col-span-2">
                            <label for="address_line_1" class="{{ $labelClass }}">{{ __('store.dealer.address_1') }} <span class="text-red-500">*</span></label>
                            <input id="address_line_1" name="address_line_1" type="text" value="{{ old('address_line_1') }}" required autocomplete="address-line1"
                                class="{{ $inputClass }} @error('address_line_1') border-red-400 @enderror">
                        </div>
                        <div class="md:col-span-2">
                            <label for="address_line_2" class="{{ $labelClass }}">{{ __('store.dealer.address_2') }}</label>
                            <input id="address_line_2" name="address_line_2" type="text" value="{{ old('address_line_2') }}" autocomplete="address-line2"
                                class="{{ $inputClass }}">
                        </div>
                        <div>
                            <label for="city" class="{{ $labelClass }}">{{ __('store.dealer.city') }} <span class="text-red-500">*</span></label>
                            <input id="city" name="city" type="text" value="{{ old('city') }}" required autocomplete="address-level2"
                                class="{{ $inputClass }} @error('city') border-red-400 @enderror">
                        </div>
                        <div>
                            <label for="postcode" class="{{ $labelClass }}">{{ __('store.dealer.postcode') }} <span class="text-red-500">*</span></label>
                            <input id="postcode" name="postcode" type="text" value="{{ old('postcode') }}" required autocomplete="postal-code"
                                class="{{ $inputClass }} @error('postcode') border-red-400 @enderror">
                        </div>
                        <div class="md:col-span-2">
                            <label for="country" class="{{ $labelClass }}">{{ __('store.dealer.country') }} <span class="text-red-500">*</span></label>
                            <input id="country" name="country" type="text" value="{{ old('country') }}" required autocomplete="country-name"
                                class="{{ $inputClass }} @error('country') border-red-400 @enderror">
                        </div>
                    </div>
                    @php $showDelivery = (string) old('different_delivery_address', '') === '1'; @endphp
                    <label class="flex items-start gap-3 cursor-pointer rounded-lg border border-slate-200 bg-slate-50/70 px-4 py-3 text-[15px] text-slate-800 hover:bg-slate-50 transition-colors">
                        <input id="different_delivery_address" type="checkbox" name="different_delivery_address" value="1"
                            class="mt-1 h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500 shrink-0"
                            @checked($showDelivery)>
                        <span>
                            <span class="font-medium text-slate-900">{{ __('store.dealer.different_delivery') }}</span>
                            <span class="mt-0.5 block text-sm font-normal text-slate-500">{{ __('store.dealer.different_delivery_hint') }}</span>
                        </span>
                    </label>
                    <div id="dealer-delivery-fields" class="rounded-xl border border-slate-200 bg-white p-4 sm:p-5 space-y-5 {{ $showDelivery ? '' : 'hidden' }}">
                        <p class="text-sm font-medium text-slate-800">{{ __('store.dealer.delivery_heading') }}</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-5">
                            <div class="md:col-span-2">
                                <label for="delivery_address_line_1" class="{{ $labelClass }}">{{ __('store.dealer.delivery_address_1') }} <span class="text-red-500">*</span></label>
                                <input id="delivery_address_line_1" name="delivery_address_line_1" type="text" value="{{ old('delivery_address_line_1') }}" autocomplete="shipping address-line1"
                                    class="{{ $inputClass }} @error('delivery_address_line_1') border-red-400 @enderror" @unless($showDelivery) disabled @endunless @if($showDelivery) required @endif>
                            </div>
                            <div class="md:col-span-2">
                                <label for="delivery_address_line_2" class="{{ $labelClass }}">{{ __('store.dealer.delivery_address_2') }}</label>
                                <input id="delivery_address_line_2" name="delivery_address_line_2" type="text" value="{{ old('delivery_address_line_2') }}" autocomplete="shipping address-line2"
                                    class="{{ $inputClass }}" @unless($showDelivery) disabled @endunless>
                            </div>
                            <div>
                                <label for="delivery_city" class="{{ $labelClass }}">{{ __('store.dealer.delivery_city') }} <span class="text-red-500">*</span></label>
                                <input id="delivery_city" name="delivery_city" type="text" value="{{ old('delivery_city') }}" autocomplete="shipping address-level2"
                                    class="{{ $inputClass }} @error('delivery_city') border-red-400 @enderror" @unless($showDelivery) disabled @endunless @if($showDelivery) required @endif>
                            </div>
                            <div>
                                <label for="delivery_postcode" class="{{ $labelClass }}">{{ __('store.dealer.delivery_postcode') }} <span class="text-red-500">*</span></label>
                                <input id="delivery_postcode" name="delivery_postcode" type="text" value="{{ old('delivery_postcode') }}" autocomplete="shipping postal-code"
                                    class="{{ $inputClass }} @error('delivery_postcode') border-red-400 @enderror" @unless($showDelivery) disabled @endunless @if($showDelivery) required @endif>
                            </div>
                            <div class="md:col-span-2">
                                <label for="delivery_country" class="{{ $labelClass }}">{{ __('store.dealer.delivery_country') }} <span class="text-red-500">*</span></label>
                                <input id="delivery_country" name="delivery_country" type="text" value="{{ old('delivery_country') }}" autocomplete="shipping country-name"
                                    class="{{ $inputClass }} @error('delivery_country') border-red-400 @enderror" @unless($showDelivery) disabled @endunless @if($showDelivery) required @endif>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Adım 3 --}}
                <div data-step="3" class="space-y-5 dealer-step-pane @unless($initialWizardStep === 3) hidden @endunless">
                    <p class="text-sm text-slate-500 max-w-3xl">{{ __('store.dealer.business_note') }}</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-5">
                        <div class="md:col-span-2">
                            <label for="business_type" class="{{ $labelClass }}">{{ __('store.dealer.business_type') }} <span class="text-red-500">*</span></label>
                            <input id="business_type" name="business_type" type="text" value="{{ old('business_type') }}" required
                                class="{{ $inputClass }} @error('business_type') border-red-400 @enderror">
                        </div>
                    </div>
                    <details class="group rounded-lg border border-slate-200 overflow-hidden md:col-span-2">
                        <summary class="cursor-pointer list-none px-4 py-3 text-sm text-slate-700 flex items-center justify-between gap-2 hover:bg-slate-50 transition-colors">
                            <span class="font-medium">{{ __('store.dealer.optional_details') }}</span>
                            <span class="text-xs text-slate-400 truncate">{{ __('store.dealer.optional_hint') }}</span>
                            <svg class="w-4 h-4 text-slate-400 group-open:rotate-180 transition-transform shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </summary>
                        <div class="px-4 pb-6 pt-1 border-t border-slate-100 bg-white">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-5 pt-4">
                                <div class="md:col-span-2">
                                    <label for="limited_company_name" class="{{ $labelClass }}">{{ __('store.dealer.limited_name') }}</label>
                                    <input id="limited_company_name" name="limited_company_name" type="text" value="{{ old('limited_company_name') }}" class="{{ $inputClass }}">
                                </div>
                                <div>
                                    <label for="company_registration_number" class="{{ $labelClass }}">{{ __('store.dealer.reg_number') }}</label>
                                    <input id="company_registration_number" name="company_registration_number" type="text" value="{{ old('company_registration_number') }}" class="{{ $inputClass }}">
                                </div>
                                <div>
                                    <label for="vat_reg_number" class="{{ $labelClass }}">{{ __('store.dealer.vat') }}</label>
                                    <input id="vat_reg_number" name="vat_reg_number" type="text" value="{{ old('vat_reg_number') }}" class="{{ $inputClass }}">
                                </div>
                                <div class="md:col-span-2">
                                    <label for="website" class="{{ $labelClass }}">{{ __('store.dealer.website') }}</label>
                                    <input id="website" name="website" type="text" value="{{ old('website') }}" placeholder="{{ __('store.dealer.website_placeholder') }}" class="{{ $inputClass }}">
                                </div>
                                @foreach([
                                    'facebook' => __('store.dealer.social_facebook'),
                                    'instagram' => __('store.dealer.social_instagram'),
                                    'twitter' => __('store.dealer.social_twitter'),
                                    'linkedin' => __('store.dealer.social_linkedin'),
                                ] as $field => $lbl)
                                    <div>
                                        <label for="{{ $field }}" class="{{ $labelClass }}">{{ $lbl }}</label>
                                        <input id="{{ $field }}" name="{{ $field }}" type="text" value="{{ old($field) }}" class="{{ $inputClass }}">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </details>
                </div>

                {{-- Adım 4 --}}
                <div data-step="4" class="space-y-8 dealer-step-pane @unless($initialWizardStep === 4) hidden @endunless">
                    <div class="grid grid-cols-1 xl:grid-cols-2 gap-x-12 gap-y-8">
                        <fieldset class="border-0 p-0 m-0 min-w-0">
                            <legend class="block text-sm font-medium text-slate-600 mb-3">{{ __('store.dealer.profile_question') }} <span class="text-red-500">*</span></legend>
                            <div class="rounded-lg border border-slate-200 divide-y divide-slate-100 overflow-hidden @error('business_profile') ring-1 ring-red-400 @enderror">
                                @foreach($businessProfiles as $value => $label)
                                    <label class="flex items-center gap-3 cursor-pointer px-4 py-3 text-[15px] text-slate-800 bg-white hover:bg-slate-50/80 transition-colors has-[:checked]:bg-slate-50">
                                        <input type="radio" name="business_profile" value="{{ $value }}" class="h-4 w-4 rounded-full border-slate-300 text-slate-800 focus:ring-slate-400 shrink-0"
                                            @checked(old('business_profile') === $value)>
                                        <span>{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </fieldset>
                        <div class="min-w-0">
                            <fieldset class="border-0 p-0 m-0">
                                <legend class="block text-sm font-medium text-slate-600 mb-3">{{ __('store.dealer.interests') }} <span class="text-red-500">*</span></legend>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-1 @error('interest_areas') rounded-lg ring-1 ring-red-400 p-2 -m-2 @enderror">
                                    @foreach($interestAreas as $value => $label)
                                        <label class="flex items-center gap-2.5 cursor-pointer py-2 text-[15px] text-slate-700">
                                            <input type="checkbox" name="interest_areas[]" value="{{ $value }}" class="rounded border-slate-300 h-4 w-4 text-slate-800 focus:ring-slate-400 shrink-0"
                                                @checked(in_array($value, $oldInterests, true))>
                                            <span>{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </fieldset>
                            <div class="mt-8">
                                <label for="how_heard_about_us" class="block text-sm font-medium text-slate-600 mb-2">{{ __('store.dealer.heard') }} <span class="text-red-500">*</span></label>
                                <input id="how_heard_about_us" name="how_heard_about_us" type="text" value="{{ old('how_heard_about_us') }}" required
                                    class="{{ $inputClass }} @error('how_heard_about_us') border-red-400 @enderror">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Adım 5 --}}
                <div data-step="5" class="space-y-5 dealer-step-pane max-w-2xl @unless($initialWizardStep === 5) hidden @endunless">
                    <p class="text-sm text-slate-500">{{ __('store.dealer.terms_intro') }}</p>
                    <label class="flex items-start gap-3 cursor-pointer text-[15px] text-slate-800">
                        <input type="checkbox" name="terms_accepted" value="1" required
                            class="mt-1 h-4 w-4 rounded border-slate-300 text-slate-800 focus:ring-slate-400 @error('terms_accepted') border-red-400 @enderror"
                            @checked(old('terms_accepted'))>
                        <span>{!! __('store.dealer.terms_accept', ['url' => route('store.legal.show', \App\Models\LegalPage::TERMS_SLUG)]) !!} <span class="text-red-500">*</span></span>
                    </label>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        {!! __('store.dealer.terms_note', ['url' => route('store.legal.show', \App\Models\LegalPage::PRIVACY_SLUG)]) !!}
                    </p>
                </div>
                </div>

                {{-- Alt aksiyonlar --}}
                <div class="mt-10 flex flex-col-reverse sm:flex-row sm:items-center gap-3 pt-6 border-t border-slate-100">
                    <a href="{{ route('home') }}" class="text-sm text-slate-500 hover:text-slate-800 px-2 py-2 text-center sm:text-left">{{ __('store.dealer.cancel') }}</a>
                    <div class="hidden sm:flex flex-1"></div>
                    <div class="flex flex-1 sm:flex-none gap-2 justify-stretch sm:justify-end">
                        <button type="button" id="dealer-wizard-prev" class="hidden rounded-lg border border-slate-200 bg-white px-5 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                            {{ __('store.dealer.back') }}
                        </button>
                        <button type="button" id="dealer-wizard-next" class="flex-1 sm:flex-none min-w-[8rem] rounded-lg bg-primary-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-700 transition-colors">
                            {{ __('store.dealer.next') }}
                        </button>
                        <button type="submit" id="dealer-wizard-submit" class="hidden flex-1 sm:flex-none min-w-[8rem] rounded-lg border border-primary-600 bg-white px-5 py-2.5 text-sm font-medium text-primary-700 hover:bg-primary-50 transition-colors">
                            {{ __('store.dealer.submit') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        (function () {
            var form = document.getElementById('dealer-wizard-form');
            if (!form) return;
            var TOTAL = 5;
            var STEP_TITLES = @json(array_values($wizardStepTitles));
            var WIZ_I18N = {
                nextLabel: @json(__('store.dealer.next_label')),
                completeLabel: @json(__('store.dealer.complete_label')),
                completeHint: @json(__('store.dealer.complete_hint')),
                validationProfile: @json(__('store.dealer.validation_select_profile')),
                validationInterest: @json(__('store.dealer.validation_interests'))
            };

            var initial = {{ (int) $initialWizardStep }};
            if (initial < 1 || initial > TOTAL) initial = 1;

            var panes = form.querySelectorAll('.dealer-step-pane');
            var progress = document.getElementById('dealer-wizard-progress');
            var stepLabel = document.getElementById('dealer-wizard-step-label');
            var currentTitle = document.getElementById('dealer-wizard-current-title');
            var nextPreview = document.getElementById('dealer-wizard-next-preview');
            var btnPrev = document.getElementById('dealer-wizard-prev');
            var btnNext = document.getElementById('dealer-wizard-next');
            var btnSubmit = document.getElementById('dealer-wizard-submit');
            var badgeNextRole = document.getElementById('dealer-badge-next-role');

            function paneFor(step) {
                return form.querySelector('.dealer-step-pane[data-step="' + step + '"]');
            }

            function getInputs(step) {
                var p = paneFor(step);
                if (!p) return [];
                return Array.prototype.slice.call(p.querySelectorAll('input, select, textarea')).filter(function (el) {
                    if (el.type === 'hidden' || el.disabled) return false;
                    if (el.closest('details') && !el.closest('details').open) return false;
                    var checkName = el.getAttribute('name');
                    return checkName && checkName.indexOf('_method') !== 0;
                });
            }

            function validatePane(step) {
                var inputs = getInputs(step);
                for (var i = 0; i < inputs.length; i++) {
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
                    el.className = 'block text-[10px] sm:text-[11px] font-medium text-center leading-snug px-px line-clamp-2';
                    if (n === step) {
                        el.classList.add('text-slate-900', 'font-semibold');
                    } else if (n < step) {
                        el.classList.add('text-slate-500');
                    } else {
                        el.classList.add('text-slate-400');
                    }
                });
            }

            function showStep(step) {
                panes.forEach(function (pane) {
                    var n = parseInt(pane.getAttribute('data-step'), 10);
                    if (n === step) pane.classList.remove('hidden');
                    else pane.classList.add('hidden');
                });
                var pct = (step / TOTAL) * 100;
                progress.style.width = pct + '%';
                stepLabel.textContent = step + ' / ' + TOTAL;
                currentTitle.textContent = STEP_TITLES[step - 1] || '';

                if (badgeNextRole) {
                    badgeNextRole.textContent = step >= TOTAL ? WIZ_I18N.completeLabel : WIZ_I18N.nextLabel;
                }

                if (step >= TOTAL) {
                    nextPreview.textContent = WIZ_I18N.completeHint;
                } else {
                    nextPreview.textContent = STEP_TITLES[step] || '';
                }

                btnPrev.classList.toggle('hidden', step <= 1);
                btnNext.classList.toggle('hidden', step >= TOTAL);
                btnSubmit.classList.toggle('hidden', step < TOTAL);

                updateStepIndicators(step);

                var p = paneFor(step);
                if (p) {
                    var firstFocus = p.querySelector('input:not([type="hidden"]), select, textarea');
                    try {
                        firstFocus && firstFocus.focus();
                    } catch (e) {}
                }
                try {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                } catch (e2) {}
            }

            btnNext.addEventListener('click', function () {
                var current = parseInt(form.dataset.currentStep, 10) || 1;
                if (!validatePane(current)) return;
                var next = Math.min(TOTAL, current + 1);
                form.dataset.currentStep = String(next);
                showStep(next);
            });

            btnPrev.addEventListener('click', function () {
                var current = parseInt(form.dataset.currentStep, 10) || 1;
                var prev = Math.max(1, current - 1);
                form.dataset.currentStep = String(prev);
                showStep(prev);
            });

            form.addEventListener('submit', function (e) {
                for (var s = 1; s <= TOTAL; s++) {
                    if (!validatePane(s)) {
                        e.preventDefault();
                        form.dataset.currentStep = String(s);
                        showStep(s);
                        return;
                    }
                }
            });

            form.dataset.currentStep = String(initial);
            showStep(initial);

            var deliveryToggle = document.getElementById('different_delivery_address');
            var deliveryFields = document.getElementById('dealer-delivery-fields');
            function syncDeliveryFields() {
                if (!deliveryToggle || !deliveryFields) return;
                var on = deliveryToggle.checked;
                deliveryFields.classList.toggle('hidden', !on);
                deliveryFields.querySelectorAll('input').forEach(function (el) {
                    el.disabled = !on;
                    if (el.id === 'delivery_address_line_2') {
                        return;
                    }
                    if (on) {
                        el.setAttribute('required', 'required');
                    } else {
                        el.removeAttribute('required');
                    }
                });
            }
            if (deliveryToggle) {
                deliveryToggle.addEventListener('change', syncDeliveryFields);
                syncDeliveryFields();
            }
        })();
    </script>
@endpush
