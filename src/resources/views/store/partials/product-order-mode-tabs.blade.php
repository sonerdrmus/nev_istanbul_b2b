{{-- Detaylı / Hızlı sipariş sekmeleri --}}
<div id="order-mode-tabs" class="mb-4 lg:mb-5" role="tablist" aria-label="{{ __('store.product.order_mode_tabs_aria') }}">
    <div class="inline-flex w-full sm:w-auto rounded-xl border border-slate-200 bg-slate-100/90 p-1 shadow-inner">
        <button type="button"
            id="order-mode-tab-detailed"
            class="order-mode-tab flex-1 sm:flex-none px-4 sm:px-6 py-2.5 sm:py-3 rounded-lg text-sm sm:text-base font-semibold transition-all bg-white text-primary-700 shadow-sm ring-1 ring-slate-200/80"
            role="tab"
            aria-selected="true"
            aria-controls="detailed-order-panel"
            data-order-mode="detailed">
            {{ __('store.product.order_mode_detailed') }}
        </button>
        <button type="button"
            id="order-mode-tab-quick"
            class="order-mode-tab flex-1 sm:flex-none px-4 sm:px-6 py-2.5 sm:py-3 rounded-lg text-sm sm:text-base font-semibold transition-all text-slate-600 hover:text-slate-800"
            role="tab"
            aria-selected="false"
            aria-controls="quick-order-panel"
            data-order-mode="quick">
            {{ __('store.product.order_mode_quick') }}
        </button>
    </div>
</div>

<div id="quick-order-panel" class="hidden" role="tabpanel" aria-labelledby="order-mode-tab-quick">
    <div class="rounded-2xl border border-slate-200/90 bg-white p-4 sm:p-5 lg:p-6 shadow-sm ring-1 ring-slate-200/40">
        <p class="text-sm sm:text-base text-slate-600 leading-relaxed mb-5">{{ __('store.product.quick_order_intro') }}</p>

        <label for="quick-order-text" class="block text-sm font-semibold text-slate-800 mb-2">{{ __('store.product.quick_order_text_label') }}</label>
        <textarea
            id="quick-order-text"
            name="quick_order_text"
            rows="8"
            maxlength="10000"
            class="w-full rounded-xl border-2 border-slate-200 bg-slate-50/60 px-4 py-3.5 text-sm sm:text-base text-slate-800 placeholder:text-slate-400 focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500/20"
            placeholder="{{ __('store.product.quick_order_text_placeholder') }}"
        ></textarea>
        <p class="mt-2 flex flex-wrap items-center justify-between gap-2 text-xs sm:text-sm">
            <span id="quick-order-char-hint" class="text-slate-500">{{ __('store.product.quick_order_min_chars_hint', ['min' => 250]) }}</span>
            <span id="quick-order-char-count" class="font-semibold tabular-nums text-slate-600">0 / 250</span>
        </p>

        <div class="mt-5">
            <label for="quick-order-image" class="block text-sm font-semibold text-slate-800 mb-2">{{ __('store.product.quick_order_image_label') }}</label>
            <input type="file"
                id="quick-order-image"
                name="quick_order_image"
                accept="image/jpeg,image/png,image/webp,image/gif"
                class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-xl file:border-0 file:bg-primary-50 file:px-4 file:py-2.5 file:text-sm file:font-semibold file:text-primary-700 hover:file:bg-primary-100">
            <p class="mt-1.5 text-xs text-slate-500">{{ __('store.product.quick_order_image_hint') }}</p>
            <div id="quick-order-image-preview-wrap" class="mt-3 hidden">
                <img id="quick-order-image-preview" src="" alt="" class="max-h-40 rounded-xl border border-slate-200 object-contain bg-slate-50">
            </div>
        </div>
    </div>
</div>
