@php
    $choice = $assignment->customizationChoice;
    $parentVariation = $product->variations->firstWhere('id', $assignment->after_product_variation_id);
    $parentName = (string) ($parentVariation->name ?? '');
    $isMultiple = (bool) $choice->allows_multiple;
    $takenNames = $product->variations->pluck('name')->map(fn ($name) => (string) $name)->all();
    $stepTitle = $choice->title;
    if (in_array($stepTitle, $takenNames, true)) {
        $stepTitle = trim($choice->title).' #'.$choice->id;
    }
    $noneLabel = __('store.product.customization_choice_none');
@endphp
<div class="product-variation-block variation-step-panel customization-choice-panel dependent-variation-block variation-step-locked flex flex-row gap-0 mt-3 lg:mt-4"
     data-variation-name="{{ $stepTitle }}"
     data-variation-label="{{ $stepTitle }}"
     data-variation-type="extra"
     data-depends-on="{{ $parentName }}"
     data-depends-on-option-ids="[]"
     data-step-index="{{ $panelStepIndex }}"
     data-allows-multiple="{{ $isMultiple ? '1' : '0' }}"
     data-multi-confirmed="0"
     data-step-unlocked="0">
    <div class="variation-timeline-cell flex flex-col items-center w-10 sm:w-11 shrink-0 pt-3 sm:pt-3.5">
        <span class="variation-step-num flex h-7 w-7 sm:h-8 sm:w-8 shrink-0 items-center justify-center rounded-full text-xs sm:text-sm font-bold ring-2 ring-white sm:ring-4 bg-slate-200 text-slate-600 shadow-sm z-10">{{ $panelStepIndex + 1 }}</span>
        <div class="w-0.5 flex-1 min-h-[6px] -mt-0.5 -mb-4 pb-4 bg-slate-200 rounded-full self-center" aria-hidden="true"></div>
    </div>
    <div class="variation-step-card flex-1 min-w-0 rounded-xl border border-slate-200/90 bg-white overflow-hidden transition-all duration-300 -ml-px shadow-sm">
        <button type="button" class="variation-step-dot w-full flex flex-row items-center gap-2.5 text-left py-3 sm:py-3.5 px-4 sm:px-5 bg-slate-50/90 hover:bg-slate-100/80 border-b border-slate-100/90 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-400 focus:ring-inset" data-step="{{ $panelStepIndex }}" aria-label="{{ $stepTitle }}">
            <span class="variation-step-name text-sm sm:text-base font-semibold text-slate-800">{{ $stepTitle }}</span>
            <span class="variation-step-check shrink-0 text-emerald-600 ml-auto" aria-hidden="true"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></span>
        </button>
        <div class="variation-step-summary hidden items-center justify-between gap-2 px-4 sm:px-5 py-2.5 sm:py-3 bg-slate-50/70 border-b border-slate-100/90">
            <div class="flex items-center gap-2 min-w-0">
                <span class="text-slate-500 text-sm">{{ $stepTitle }}:</span>
                <span class="variation-step-summary-value font-medium text-slate-800 truncate">—</span>
            </div>
            <button type="button" class="variation-step-change-btn text-sm font-medium text-primary-600 hover:text-primary-700">{{ __('store.product.change') }}</button>
        </div>
        <div class="variation-step-full p-3.5 sm:p-4 lg:px-5 lg:py-4 hidden">
            @if(filled($choice->description))
                <p class="mb-3 whitespace-pre-wrap text-sm leading-relaxed text-slate-600">{{ $choice->description }}</p>
            @endif
            <style>
                .cc-option-img { display: block; flex-shrink: 0; border-radius: 0.75rem; object-fit: cover; background: #f1f5f9; }
                .cc-option-img-small { width: 3.5rem; height: 3.5rem; }
                .cc-option-img-medium { width: 6.5rem; height: 6.5rem; }
                .cc-option-img-large { width: 100%; height: 11.5rem; }
            </style>
            <div class="grid grid-cols-1 gap-2.5 sm:grid-cols-2">
                @foreach($choice->options as $option)
                    @php
                        $imageUrl = filled($option->image_path) ? \App\Support\MediaUrl::public($option->image_path) : '';
                        $imageSize = $option->normalizedImageSize();
                    @endphp
                    <button type="button"
                        class="product-option flex w-full gap-3 rounded-xl border-2 border-slate-300 px-3 py-2.5 text-left text-sm font-semibold text-slate-700 hover:bg-slate-50 sm:text-base {{ $imageSize === 'large' ? 'flex-col items-stretch' : 'items-center' }}"
                        data-option="{{ $option->label }}"
                        data-option-label="{{ $option->label }}"
                        data-option-id="{{ $option->id }}"
                        data-option-solo="0"
                        data-price-delta="1"
                        @if($imageUrl !== '') data-option-image-url="{{ $imageUrl }}" @endif>
                        @if($imageUrl !== '')
                            <img src="{{ $imageUrl }}" alt="" class="cc-option-img cc-option-img-{{ $imageSize }}">
                        @endif
                        <span class="min-w-0 {{ $imageSize === 'large' ? 'text-center' : '' }}">{{ $option->label }}</span>
                    </button>
                @endforeach
                <button type="button"
                    class="product-option flex w-full items-center gap-3 rounded-xl border-2 border-dashed border-slate-300 px-3 py-2.5 text-left text-sm font-semibold text-slate-600 hover:bg-slate-50 sm:text-base"
                    data-option="{{ \App\Models\CustomizationChoice::NONE_LABEL }}"
                    data-option-label="{{ $noneLabel }}"
                    data-option-solo="1"
                    data-price-delta="1">
                    <span class="min-w-0">{{ $noneLabel }}</span>
                </button>
            </div>
            @if($isMultiple)
                <div class="variation-multi-continue-wrap mt-4 border-t border-slate-100 pt-3">
                    <p class="mb-2 text-xs text-slate-500">{{ __('store.product.variation_multi_hint') }}</p>
                    <button type="button" class="variation-multi-continue-btn w-full rounded-xl bg-primary-600 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-700 disabled:cursor-not-allowed disabled:opacity-50 sm:py-3" disabled>{{ __('store.product.variation_continue') }}</button>
                </div>
            @endif
        </div>
    </div>
</div>
