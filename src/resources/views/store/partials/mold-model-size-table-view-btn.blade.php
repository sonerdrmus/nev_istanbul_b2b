@if(!empty($sizeTableImageUrl))
    <div class="mold-model-size-table-btn-wrap border-t border-slate-200/90 bg-gradient-to-b from-slate-50/95 to-white px-1.5 py-1.5 sm:px-2">
        <button type="button"
            class="mold-model-size-table-btn relative z-[1] flex w-full items-center justify-center gap-1 rounded-md px-1.5 py-1 text-[10px] font-semibold leading-snug text-sky-800 transition-colors hover:bg-sky-50/90 hover:text-sky-950 focus:outline-none focus:ring-2 focus:ring-sky-400/40 focus:ring-inset sm:text-xs"
            data-image-url="{{ $sizeTableImageUrl }}"
            data-image-title="{{ __('store.product.mold_model_size_table_modal_title', ['name' => $optionTitle]) }}"
            data-image-alt="{{ $optionTitle }}"
            aria-label="{{ __('store.product.mold_model_size_table_view_aria', ['name' => $optionTitle]) }}">
            <svg class="h-3.5 w-3.5 shrink-0 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18M10 3v18M14 3v18"/></svg>
            <span class="truncate">{{ __('store.product.mold_model_size_table_view') }}</span>
        </button>
    </div>
@endif
