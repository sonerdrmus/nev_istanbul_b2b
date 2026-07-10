{{-- Sol sütun: hızlı sipariş özeti --}}
<div id="quick-order-sidebar" class="mt-4 lg:mt-5 hidden rounded-2xl border border-slate-200/90 bg-white p-4 shadow-sm ring-1 ring-slate-900/5 sm:p-5">
    <p class="mb-3 flex items-center gap-2.5 text-sm font-semibold text-slate-900">
        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-primary-600/10 text-primary-700">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        </span>
        {{ __('store.product.order_mode_quick') }}
    </p>
    <div class="rounded-xl border border-slate-200/90 bg-slate-50/50 p-3.5 space-y-3">
        <div>
            <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-500 mb-1">{{ __('store.product.quick_order_text_label') }}</p>
            <p id="quick-order-sidebar-text" class="text-sm text-slate-700 leading-relaxed line-clamp-6 whitespace-pre-line">—</p>
        </div>
        <div>
            <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-500 mb-1.5">{{ __('store.product.quick_order_image_label') }}</p>
            <div id="quick-order-sidebar-image-empty" class="text-sm text-slate-400">{{ __('store.product.quick_order_image_missing') }}</div>
            <img id="quick-order-sidebar-image" src="" alt="" class="hidden max-h-28 rounded-lg border border-slate-200 object-contain bg-white">
        </div>
    </div>
    <p id="quick-order-sidebar-warning" class="mt-3 text-sm text-amber-700 font-medium hidden" role="alert">
        {{ __('store.product.quick_order_incomplete_hint') }}
    </p>
</div>
