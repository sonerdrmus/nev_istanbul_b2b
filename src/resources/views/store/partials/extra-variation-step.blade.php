@php
    $extra = $assignment->extraVariation;
    $parentVariation = $product->variations->firstWhere('id', $assignment->after_product_variation_id);
    $parentName = (string) ($parentVariation->name ?? '');
    $isMultiple = $extra->answer_type === \App\Models\ExtraVariation::TYPE_RADIO_MULTIPLE;
    $takenNames = $product->variations->pluck('name')->map(fn ($name) => (string) $name)->all();
    $stepTitle = $extra->title;
    if (in_array($stepTitle, $takenNames, true)) {
        $stepTitle = trim($extra->title).' #'.$extra->id;
    }
    $stepQuestion = $extra->question !== '' ? $extra->question : $extra->title;
@endphp
<div class="product-variation-block variation-step-panel extra-variation-panel dependent-variation-block variation-step-locked flex flex-row gap-0 mt-3 lg:mt-4"
     data-extra-variation="1"
     data-extra-answer="{{ $extra->answer_type }}"
     data-extra-confirmed="0"
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
            <p class="text-sm sm:text-base font-semibold text-slate-800 mb-3">{{ $stepQuestion }}</p>
            @if($extra->isRadio())
                <div class="space-y-2">
                    @foreach($extra->options as $option)
                        <button type="button" class="product-option w-full text-left px-4 py-3 rounded-xl border-2 border-slate-300 text-sm sm:text-base font-semibold text-slate-700 hover:bg-slate-50 min-h-[3rem]" data-option="{{ $option->label }}" data-option-label="{{ $option->label }}" data-option-id="{{ $option->id }}" data-price-delta="1">{{ $option->label }}</button>
                    @endforeach
                </div>
                @if($isMultiple)
                    <div class="variation-multi-continue-wrap mt-4 pt-3 border-t border-slate-100">
                        <p class="text-xs text-slate-500 mb-2">{{ __('store.product.variation_multi_hint') }}</p>
                        <button type="button" class="variation-multi-continue-btn w-full py-2.5 sm:py-3 rounded-xl bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold shadow-sm disabled:opacity-50 disabled:cursor-not-allowed" disabled>{{ __('store.product.variation_continue') }}</button>
                    </div>
                @endif
            @elseif($extra->answer_type === \App\Models\ExtraVariation::TYPE_TEXTAREA)
                <textarea rows="4" maxlength="2000" class="extra-variation-textarea w-full rounded-xl border-2 border-slate-300 bg-white px-3.5 py-3 text-sm sm:text-base text-slate-800 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/25"></textarea>
                <button type="button" class="extra-variation-continue-btn mt-3 w-full py-2.5 sm:py-3 rounded-xl bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold shadow-sm disabled:opacity-50 disabled:cursor-not-allowed" disabled>{{ __('store.product.variation_continue') }}</button>
            @else
                <div class="extra-variation-info-text whitespace-pre-wrap rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm leading-relaxed text-slate-700">{{ $extra->info_text }}</div>
                <button type="button" class="extra-variation-continue-btn mt-3 w-full py-2.5 sm:py-3 rounded-xl bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold shadow-sm">{{ __('store.product.variation_continue') }}</button>
            @endif
        </div>
    </div>
</div>
