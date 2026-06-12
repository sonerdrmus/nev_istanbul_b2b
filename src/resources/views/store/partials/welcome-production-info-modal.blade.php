{{-- İlk ziyarette bir kez: made-to-order üretim bilgilendirmesi --}}
<div id="production-info-modal" class="hidden fixed inset-0 z-[68] flex items-center justify-center p-4 sm:p-6" aria-modal="true" role="dialog" aria-labelledby="production-info-modal-title" aria-describedby="production-info-modal-body">
    <div id="production-info-modal-backdrop" class="absolute inset-0 bg-slate-900/65 backdrop-blur-sm production-info-backdrop"></div>
    <div class="relative w-full max-w-2xl sm:max-w-3xl bg-white rounded-2xl sm:rounded-3xl shadow-2xl border border-slate-200/80 overflow-hidden production-info-panel">
        <button type="button" id="production-info-modal-close" class="absolute top-3 right-3 z-10 p-2 rounded-full bg-white/95 hover:bg-white text-slate-500 hover:text-slate-700 shadow-md ring-1 ring-slate-200/80 transition-colors" aria-label="{{ __('store.production_info.close') }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        <div class="relative bg-gradient-to-b from-slate-50 to-white border-b border-slate-100 px-5 sm:px-8 pt-6 pb-5 sm:pt-7 sm:pb-6">
            <img
                src="{{ asset('images/nevistanbul-brand-banner.jpg') }}"
                alt="{{ __('store.production_info.banner_alt') }}"
                class="w-full h-auto max-h-[6rem] sm:max-h-[7.5rem] object-contain object-center mx-auto select-none"
                width="1024"
                height="139"
                loading="eager"
                decoding="async"
            >
        </div>

        <div class="px-6 sm:px-8 pt-6 pb-2">
            <p class="text-xs font-semibold uppercase tracking-wider text-primary-600 mb-1.5">{{ __('store.production_info.badge') }}</p>
            <h2 id="production-info-modal-title" class="text-xl sm:text-2xl font-bold text-slate-900 leading-snug">{{ __('store.production_info.title') }}</h2>
        </div>

        <div id="production-info-modal-body" class="px-6 sm:px-8 pb-2">
            <p class="text-slate-600 text-[15px] sm:text-base leading-relaxed">{{ __('store.production_info.paragraph_1') }}</p>
            <p class="mt-4 text-slate-600 text-[15px] sm:text-base leading-relaxed">{{ __('store.production_info.paragraph_2') }}</p>

            <ul class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-2.5" aria-hidden="true">
                <li class="flex items-center gap-2.5 rounded-xl bg-slate-50 border border-slate-100 px-3 py-2.5">
                    <span class="flex-shrink-0 w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-primary-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                    </span>
                    <span class="text-xs font-semibold text-slate-700">{{ __('store.production_info.highlight_production') }}</span>
                </li>
                <li class="flex items-center gap-2.5 rounded-xl bg-slate-50 border border-slate-100 px-3 py-2.5">
                    <span class="flex-shrink-0 w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-primary-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                    </span>
                    <span class="text-xs font-semibold text-slate-700">{{ __('store.production_info.highlight_certification') }}</span>
                </li>
                <li class="flex items-center gap-2.5 rounded-xl bg-slate-50 border border-slate-100 px-3 py-2.5">
                    <span class="flex-shrink-0 w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-primary-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                    </span>
                    <span class="text-xs font-semibold text-slate-700">{{ __('store.production_info.highlight_delivery') }}</span>
                </li>
            </ul>
        </div>

        <div class="px-6 sm:px-8 py-6 sm:py-7 border-t border-slate-100 mt-4 bg-slate-50/50">
            <button type="button" id="production-info-modal-confirm" class="w-full px-5 py-3.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-semibold shadow-sm hover:shadow-md transition-all text-sm sm:text-base">
                {{ __('store.production_info.confirm') }}
            </button>
            <p class="mt-3 text-center text-xs text-slate-400">{{ __('store.production_info.once_note') }}</p>
        </div>
    </div>
</div>

<style>
    #production-info-modal:not(.hidden) .production-info-backdrop {
        animation: productionInfoFadeIn 0.28s ease-out forwards;
    }
    #production-info-modal:not(.hidden) .production-info-panel {
        animation: productionInfoSlideUp 0.32s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    @keyframes productionInfoFadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    @keyframes productionInfoSlideUp {
        from { opacity: 0; transform: translateY(1rem) scale(0.98); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
</style>

<script>
(function () {
    var storageKey = 'nev_istanbul_production_info_v1';
    var modal = document.getElementById('production-info-modal');
    if (!modal) return;

    function dismiss() {
        try { localStorage.setItem(storageKey, '1'); } catch (e) {}
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function open() {
        if (document.getElementById('dealer-success-modal') && !document.getElementById('dealer-success-modal').classList.contains('hidden')) {
            return;
        }
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    document.getElementById('production-info-modal-close')?.addEventListener('click', dismiss);
    document.getElementById('production-info-modal-confirm')?.addEventListener('click', dismiss);
    document.getElementById('production-info-modal-backdrop')?.addEventListener('click', dismiss);

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) dismiss();
    });

    document.addEventListener('DOMContentLoaded', function () {
        var seen = false;
        try { seen = localStorage.getItem(storageKey) === '1'; } catch (e) {}
        if (!seen) {
            setTimeout(open, 400);
        }
    });
})();
</script>
