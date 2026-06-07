@if(!empty($sizeTableImageUrl))
    <div class="border-t border-slate-200/90 bg-gradient-to-b from-slate-50/95 to-white px-2 py-2 sm:px-2.5">
        <button type="button"
            class="mold-model-size-table-btn relative z-[1] flex w-full items-center justify-center gap-1.5 rounded-md px-2 py-1.5 text-xs font-semibold leading-snug text-sky-800 transition-colors hover:bg-sky-50/90 hover:text-sky-950 focus:outline-none focus:ring-2 focus:ring-sky-400/40 focus:ring-inset sm:text-sm"
            data-image-url="{{ $sizeTableImageUrl }}"
            data-image-title="{{ __('store.product.mold_model_size_table_modal_title', ['name' => $optionTitle]) }}"
            data-image-alt="{{ $optionTitle }}"
            aria-label="{{ __('store.product.mold_model_size_table_view_aria', ['name' => $optionTitle]) }}">
            <svg class="h-4 w-4 shrink-0 text-sky-600 sm:h-[1.125rem] sm:w-[1.125rem]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18M10 3v18M14 3v18"/></svg>
            <span class="truncate">{{ __('store.product.mold_model_size_table_view') }}</span>
        </button>
    </div>
@endif
