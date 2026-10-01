@php
    $data = is_array($item->variation_data ?? null) ? $item->variation_data : [];
    $sizeParts = [];
    if (isset($data['size_quantities']) && is_array($data['size_quantities'])) {
        $sizeParts = array_filter($data['size_quantities'], fn ($qty) => (int) $qty > 0);
    }
    $custRows = [];
    $custTable = $data['product_customization_table'] ?? null;
    if (is_array($custTable)) {
        $custRows = $custTable['rows'] ?? (isset($custTable['row_id']) ? [$custTable] : []);
        $custRows = array_values(array_filter($custRows, fn ($row) => is_array($row)));
    }
    $notes = is_string($data['product_customization_notes'] ?? null) ? trim($data['product_customization_notes']) : '';
    $quick = is_array($data['quick_order'] ?? null) ? $data['quick_order'] : [];
@endphp

<div class="space-y-3">
    @if(count($sizeParts) > 0)
        <section class="overflow-hidden rounded-xl border border-slate-200/90 bg-white">
            <header class="border-b border-slate-100 bg-slate-50/90 px-4 py-2.5">
                <h4 class="text-[11px] font-bold uppercase tracking-wider text-slate-500">{{ __('store.order_confirmation.sizes') }}</h4>
            </header>
            <div class="flex flex-wrap gap-2 px-4 py-3">
                @foreach($sizeParts as $size => $qty)
                    <span class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-1.5 text-sm">
                        <span class="font-semibold text-slate-900">{{ $size }}</span>
                        <span class="text-slate-500">{{ (int) $qty }} {{ __('store.cart.units') }}</span>
                    </span>
                @endforeach
            </div>
        </section>
    @endif

    @foreach($data as $optName => $optValue)
        @continue(in_array($optName, ['size_quantities', 'product_customization_table', 'product_customization_notes', 'quick_order', 'product_customization'], true))
        @php
            $groupTitle = \App\Support\CatalogLabelTranslator::label((string) $optName);
            $sections = [];
            if (is_array($optValue) && array_is_list($optValue)) {
                foreach ($optValue as $child) {
                    $childRows = \App\Support\VariationSelectionDisplay::rows($child);
                    if ($childRows !== []) {
                        $sections[] = $childRows;
                    }
                }
            } elseif (is_array($optValue)) {
                $rows = \App\Support\VariationSelectionDisplay::rows($optValue);
                if ($rows !== []) {
                    $sections[] = $rows;
                }
            } else {
                $text = \App\Support\VariationSelectionDisplay::rows($optValue);
                if ($text !== []) {
                    $sections[] = $text;
                }
            }
        @endphp
        @if($sections !== [])
            <section class="overflow-hidden rounded-xl border border-slate-200/90 bg-white">
                <header class="border-b border-slate-100 bg-slate-50/90 px-4 py-2.5">
                    <h4 class="text-[11px] font-bold uppercase tracking-wider text-slate-500">{{ $groupTitle }}</h4>
                </header>
                <div class="divide-y divide-slate-100">
                    @foreach($sections as $sectionIndex => $sectionRows)
                        <div class="px-4 py-3">
                            @if(count($sections) > 1)
                                <p class="mb-2 text-[11px] font-semibold uppercase tracking-wide text-primary-700">{{ __('store.order_confirmation.selection_n', ['n' => $sectionIndex + 1]) }}</p>
                            @endif
                            <dl class="space-y-3">
                                @foreach($sectionRows as $detailRow)
                                    @if(!empty($detailRow['divider']))
                                        <div class="border-t border-slate-100"></div>
                                    @elseif(($detailRow['value'] ?? '') !== '')
                                        <div>
                                            @if(($detailRow['label'] ?? '') !== '')
                                                <dt class="text-xs font-medium text-slate-500">{{ $detailRow['label'] }}</dt>
                                            @endif
                                            <dd class="mt-0.5 whitespace-pre-wrap break-words text-sm font-semibold leading-snug text-slate-900">{{ $detailRow['value'] }}</dd>
                                        </div>
                                    @endif
                                @endforeach
                            </dl>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
    @endforeach

    @if(($data['product_customization'] ?? null) === 'skipped')
        <section class="overflow-hidden rounded-xl border border-slate-200/90 bg-white">
            <header class="border-b border-slate-100 bg-slate-50/90 px-4 py-2.5">
                <h4 class="text-[11px] font-bold uppercase tracking-wider text-slate-500">{{ __('store.product.customization_summary_section_label') }}</h4>
            </header>
            <p class="px-4 py-3 text-sm font-medium text-slate-700">{{ __('store.product.skip_customization') }}</p>
        </section>
    @endif

    @if($custRows !== [])
        <section class="overflow-hidden rounded-xl border border-slate-200/90 bg-white">
            <header class="border-b border-slate-100 bg-slate-50/90 px-4 py-2.5">
                <h4 class="text-[11px] font-bold uppercase tracking-wider text-slate-500">{{ __('store.product.customization_summary_section_label') }}</h4>
            </header>
            <div class="space-y-3 p-3 sm:p-4">
                @foreach($custRows as $crowIndex => $crow)
                    @php
                        $position = trim((string) ($crow['konum_label'] ?? ''));
                        if ($position === '') {
                            $position = \App\Support\CatalogLabelTranslator::label((string) ($crow['konum'] ?? ''));
                        }
                        $area = '';
                        if (! empty($crow['alan_cm2_display'])) {
                            $area = __('store.product.customization_area_cm2', ['area' => $crow['alan_cm2_display']]);
                        } elseif (! empty($crow['alan_m2_display'])) {
                            $area = __('store.product.customization_area_sqm', ['area' => $crow['alan_m2_display']]);
                        }
                        $printFields = array_values(array_filter([
                            ['label' => __('store.product.customization_col_position'), 'value' => $position],
                            ['label' => __('store.product.customization_summary_dim'), 'value' => trim((string) ($crow['en_boy_cm'] ?? ''))],
                            ['label' => __('store.product.customization_summary_area'), 'value' => $area],
                            ['label' => __('store.product.customization_summary_ebat'), 'value' => trim((string) ($crow['ebat'] ?? ''))],
                            ['label' => __('store.product.customization_summary_colors'), 'value' => trim((string) ($crow['renk_sayisi'] ?? '')) !== '' ? trim((string) $crow['renk_sayisi']).' '.__('store.product.customization_colors_unit') : ''],
                            ['label' => __('store.product.customization_summary_print'), 'value' => \App\Support\CatalogLabelTranslator::label((string) ($crow['baski_teknigi'] ?? ''))],
                        ], fn (array $field): bool => $field['value'] !== ''));
                    @endphp
                    @if($printFields !== [])
                        <article class="overflow-hidden rounded-xl border border-slate-200/80 bg-slate-50/40">
                            @if(count($custRows) > 1)
                                <p class="border-b border-slate-100 px-3.5 py-2 text-[11px] font-semibold uppercase tracking-wide text-primary-700">{{ __('store.order_confirmation.print_n', ['n' => $crowIndex + 1]) }}</p>
                            @endif
                            <dl class="grid grid-cols-2 divide-x divide-y divide-slate-100 sm:grid-cols-3">
                                @foreach($printFields as $field)
                                    <div class="px-3.5 py-2.5">
                                        <dt class="text-[10px] font-semibold uppercase tracking-wider text-slate-500">{{ $field['label'] }}</dt>
                                        <dd class="mt-1 text-sm font-semibold leading-snug text-slate-900">{{ $field['value'] }}</dd>
                                    </div>
                                @endforeach
                            </dl>
                        </article>
                    @endif
                @endforeach
            </div>
        </section>
    @endif

    @if($notes !== '')
        <section class="overflow-hidden rounded-xl border border-slate-200/90 bg-white">
            <header class="border-b border-slate-100 bg-slate-50/90 px-4 py-2.5">
                <h4 class="text-[11px] font-bold uppercase tracking-wider text-slate-500">{{ __('store.product.customization_panel_title') }}</h4>
            </header>
            <p class="whitespace-pre-wrap break-words px-4 py-3 text-sm font-medium leading-relaxed text-slate-800">{{ $notes }}</p>
        </section>
    @endif

    @if(trim((string) ($quick['notes'] ?? '')) !== '' || trim((string) ($quick['image_url'] ?? '')) !== '')
        <section class="overflow-hidden rounded-xl border border-slate-200/90 bg-white">
            <header class="border-b border-slate-100 bg-slate-50/90 px-4 py-2.5">
                <h4 class="text-[11px] font-bold uppercase tracking-wider text-slate-500">{{ __('store.product.quick_order_summary') }}</h4>
            </header>
            <div class="space-y-2 px-4 py-3">
                @if(trim((string) ($quick['notes'] ?? '')) !== '')
                    <p class="whitespace-pre-wrap break-words text-sm font-medium leading-relaxed text-slate-800">{{ $quick['notes'] }}</p>
                @endif
                @if(trim((string) ($quick['image_url'] ?? '')) !== '')
                    <a href="{{ $quick['image_url'] }}" target="_blank" rel="noopener" class="inline-flex text-sm font-semibold text-primary-700 hover:text-primary-800">{{ __('store.product.quick_order_image_label') }}</a>
                @endif
            </div>
        </section>
    @endif
</div>
