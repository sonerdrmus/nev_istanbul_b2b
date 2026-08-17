@extends('store.layout')

@section('title', $page->localized_title)

@section('content')
    @php
        $user = auth()->user();
        $inputClass = 'w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-[15px] leading-normal text-slate-900 placeholder:text-slate-400 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 transition-colors outline-none';
        $labelClass = 'block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-1.5';
        $maxAttachments = (int) config('store.contact.max_attachments', 5);
        $maxMb = max(1, (int) ceil(((int) config('store.contact.max_kilobytes', 8192)) / 1024));
        $defaultName = old('name', $user?->name);
        $defaultEmail = old('email', $user?->email);
        $defaultCompany = old('company', $user?->company?->name);
        $mapQuery = (string) config('store.contact.map_query');
        $mapHl = match (app()->getLocale()) {
            'tr' => 'tr',
            'it' => 'it',
            default => 'en',
        };
        $mapEmbedUrl = 'https://maps.google.com/maps?q='.rawurlencode($mapQuery).'&hl='.$mapHl.'&z=16&output=embed';
        $mapOpenUrl = 'https://www.google.com/maps/search/?api=1&query='.rawurlencode($mapQuery);
        $mapDirectionsUrl = 'https://www.google.com/maps/dir/?api=1&destination='.rawurlencode($mapQuery);
    @endphp

    <style>
        .contact-honeypot { position: absolute; left: -10000px; width: 1px; height: 1px; overflow: hidden; }
        .contact-map iframe { filter: grayscale(18%) saturate(1.05) contrast(1.04); }
    </style>

    <div class="space-y-8 lg:space-y-10">
        <header class="max-w-3xl">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-primary-600">{{ __('store.contact.page_kicker') }}</p>
            <h1 class="mt-2 text-2xl sm:text-3xl lg:text-[2rem] font-semibold tracking-tight text-slate-900">{{ __('store.contact.page_title') }}</h1>
            <p class="mt-3 text-sm sm:text-[0.95rem] leading-relaxed text-slate-500">{{ __('store.contact.page_lead') }}</p>
        </header>

        <section class="contact-map relative overflow-hidden rounded-3xl border border-slate-200/80 shadow-[0_18px_50px_-28px_rgba(15,23,42,0.35)] h-[280px] sm:h-[380px] lg:h-[440px] bg-slate-100" aria-label="{{ __('store.contact.map_title') }}">
            <iframe
                title="{{ __('store.contact.map_title') }}"
                src="{{ $mapEmbedUrl }}"
                class="absolute inset-0 h-full w-full border-0"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                allowfullscreen
            ></iframe>
            <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-slate-950/45 via-slate-950/5 to-transparent"></div>
            <div class="absolute bottom-4 left-4 right-4 sm:bottom-6 sm:left-6 sm:right-auto sm:max-w-md">
                <div class="rounded-2xl border border-white/70 bg-white/95 shadow-xl backdrop-blur-md px-4 py-4 sm:px-5">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400">{{ __('store.contact.address_label') }}</p>
                    <p class="mt-1.5 text-sm font-semibold leading-snug text-slate-900">{{ __('store.footer.company_name') }}</p>
                    <p class="mt-1 text-sm leading-relaxed text-slate-600">{{ __('store.footer.company_address') }}</p>
                    <div class="mt-3.5 flex flex-wrap gap-2">
                        <a href="{{ $mapDirectionsUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 rounded-full bg-primary-600 px-3.5 py-1.5 text-xs font-semibold text-white hover:bg-primary-700 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            {{ __('store.contact.directions') }}
                        </a>
                        <a href="{{ $mapOpenUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white px-3.5 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                            {{ __('store.contact.open_maps') }}
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">
            <aside class="lg:col-span-5 space-y-4">
                <div class="rounded-3xl border border-slate-200 bg-white p-5 sm:p-7 shadow-sm">
                    <ul class="space-y-5">
                        <li class="flex gap-3.5">
                            <span class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-primary-50 text-primary-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </span>
                            <div>
                                <p class="text-[11px] font-semibold uppercase tracking-widest text-slate-400">{{ __('store.contact.address_label') }}</p>
                                <p class="mt-1 text-sm leading-relaxed text-slate-800">{{ __('store.footer.company_address') }}</p>
                            </div>
                        </li>
                        <li class="flex gap-3.5">
                            <span class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-primary-50 text-primary-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </span>
                            <div>
                                <p class="text-[11px] font-semibold uppercase tracking-widest text-slate-400">{{ __('store.contact.email_label') }}</p>
                                <a href="mailto:{{ __('store.footer.company_email') }}" class="mt-1 block text-sm font-medium text-primary-700 hover:text-primary-800">{{ __('store.footer.company_email') }}</a>
                                <p class="mt-2 text-[11px] font-semibold uppercase tracking-widest text-slate-400">{{ __('store.contact.privacy_email_label') }}</p>
                                <a href="mailto:{{ __('store.footer.company_privacy_email') }}" class="mt-1 block text-sm text-slate-700 hover:text-primary-700">{{ __('store.footer.company_privacy_email') }}</a>
                            </div>
                        </li>
                        <li class="flex gap-3.5">
                            <span class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-primary-50 text-primary-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-7 8h8a2 2 0 002-2V8.5a2 2 0 00-.586-1.414l-4.5-4.5A2 2 0 0012.5 2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            </span>
                            <div>
                                <p class="text-[11px] font-semibold uppercase tracking-widest text-slate-400">{{ __('store.contact.tax_label') }}</p>
                                <p class="mt-1 text-sm leading-relaxed text-slate-800">{{ __('store.footer.company_tax') }}</p>
                            </div>
                        </li>
                        <li class="flex gap-3.5">
                            <span class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-primary-50 text-primary-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                            </span>
                            <div>
                                <p class="text-[11px] font-semibold uppercase tracking-widest text-slate-400">{{ __('store.contact.website_label') }}</p>
                                <a href="{{ __('store.contact.website_url') }}" target="_blank" rel="noopener noreferrer" class="mt-1 block text-sm font-medium text-primary-700 hover:text-primary-800">{{ __('store.contact.website') }}</a>
                            </div>
                        </li>
                    </ul>
                </div>

                <a href="{{ route('store.dealer-registration') }}" class="group flex items-center justify-between gap-4 rounded-3xl border border-slate-200 bg-gradient-to-br from-slate-900 to-slate-800 px-5 py-4 sm:px-6 text-white shadow-sm hover:from-slate-800 hover:to-slate-700 transition-colors">
                    <span>
                        <span class="block text-sm font-semibold">{{ __('store.contact.dealer_cta') }}</span>
                        <span class="mt-1 block text-xs text-slate-300">{{ __('store.contact.dealer_hint') }}</span>
                    </span>
                    <svg class="w-5 h-5 shrink-0 text-primary-300 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </aside>

            <section id="contact-form" class="lg:col-span-7 scroll-mt-24">
                <div class="rounded-3xl border border-slate-200 bg-white px-5 py-7 sm:px-8 sm:py-9 shadow-sm">
                    <h2 class="text-lg sm:text-xl font-semibold text-slate-900 tracking-tight">{{ __('store.contact.form_title') }}</h2>
                    <p class="mt-1 text-sm text-slate-500">{{ __('store.contact.form_intro') }}</p>

                    @if($errors->any())
                        <div class="mt-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                            {{ __('store.contact.validation_intro') }}
                        </div>
                    @endif

                    <form action="{{ route('store.contact.send') }}" method="post" enctype="multipart/form-data" class="mt-6 space-y-5">
                        @csrf

                        <div class="contact-honeypot" aria-hidden="true">
                            <label for="website">Website</label>
                            <input id="website" type="text" name="website" value="" tabindex="-1" autocomplete="off">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label for="name" class="{{ $labelClass }}">{{ __('store.contact.name') }} <span class="text-red-500">*</span></label>
                                <input id="name" name="name" type="text" value="{{ $defaultName }}" required autocomplete="name"
                                    class="{{ $inputClass }} @error('name') border-red-400 @enderror">
                                @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="email" class="{{ $labelClass }}">{{ __('store.contact.email') }} <span class="text-red-500">*</span></label>
                                <input id="email" name="email" type="email" value="{{ $defaultEmail }}" required autocomplete="email"
                                    class="{{ $inputClass }} @error('email') border-red-400 @enderror">
                                @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="phone" class="{{ $labelClass }}">{{ __('store.contact.phone') }}</label>
                                <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" autocomplete="tel"
                                    class="{{ $inputClass }} @error('phone') border-red-400 @enderror">
                                @error('phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="company" class="{{ $labelClass }}">{{ __('store.contact.company') }}</label>
                                <input id="company" name="company" type="text" value="{{ $defaultCompany }}" autocomplete="organization"
                                    class="{{ $inputClass }} @error('company') border-red-400 @enderror">
                                @error('company') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label for="subject" class="{{ $labelClass }}">{{ __('store.contact.subject') }}</label>
                            <input id="subject" name="subject" type="text" value="{{ old('subject') }}"
                                class="{{ $inputClass }} @error('subject') border-red-400 @enderror">
                            @error('subject') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="message" class="{{ $labelClass }}">{{ __('store.contact.message') }} <span class="text-red-500">*</span></label>
                            <textarea id="message" name="message" rows="6" required
                                class="{{ $inputClass }} resize-y min-h-[8.5rem] @error('message') border-red-400 @enderror">{{ old('message') }}</textarea>
                            @error('message') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="attachments" class="{{ $labelClass }}">{{ __('store.contact.attachments') }}</label>
                            <label for="attachments" class="mt-0.5 flex cursor-pointer flex-col items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-slate-50/80 px-4 py-6 text-center hover:border-primary-300 hover:bg-primary-50/40 transition-colors">
                                <svg class="mb-2 h-6 w-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                <span class="text-sm font-medium text-slate-700">{{ __('store.contact.attachments') }}</span>
                                <span class="mt-1 text-xs text-slate-500">{{ __('store.contact.attachments_hint', ['count' => $maxAttachments, 'size' => $maxMb]) }}</span>
                                <input id="attachments" name="attachments[]" type="file" multiple
                                    accept=".pdf,.jpg,.jpeg,.png,.webp,.doc,.docx,.xls,.xlsx,.zip"
                                    class="sr-only">
                            </label>
                            <p id="contact-file-names" class="mt-2 hidden text-xs text-slate-600"></p>
                            @error('attachments') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            @error('attachments.*') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="pt-1">
                            <button type="submit" class="inline-flex w-full sm:w-auto items-center justify-center rounded-xl bg-primary-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-primary-700 shadow-sm shadow-primary-600/20 transition-colors">
                                {{ __('store.contact.submit') }}
                            </button>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </div>

    <script>
    (function () {
        var input = document.getElementById('attachments');
        var names = document.getElementById('contact-file-names');
        if (!input || !names) return;
        input.addEventListener('change', function () {
            var files = Array.prototype.slice.call(input.files || []);
            if (!files.length) {
                names.classList.add('hidden');
                names.textContent = '';
                return;
            }
            names.textContent = files.map(function (f) { return f.name; }).join(' · ');
            names.classList.remove('hidden');
        });
    })();
    </script>
@endsection
