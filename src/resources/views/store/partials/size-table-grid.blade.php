@php
    /** @var \App\Models\SizeTable $sizeTable */
    $wrapClass = $wrapClass ?? 'hidden mt-4 first:mt-0 size-table-wrap';
    $wrapId = $wrapId ?? ($sizeTable->slug.'-size-table-wrap');
    $wrapExtraAttrs = $wrapExtraAttrs ?? '';
@endphp
<div id="{{ $wrapId }}" class="{{ $wrapClass }}" data-slug="{{ $sizeTable->slug }}" data-trigger-variation="{{ e($sizeTable->trigger_variation_name ?? '') }}" data-trigger-value="{{ e($sizeTable->trigger_option_value ?? '') }}" {!! $wrapExtraAttrs !!}>
    <p class="text-base sm:text-lg font-semibold text-slate-700 mb-3 sm:mb-4 flex items-center gap-2">
        <span class="h-px flex-1 max-w-[40px] rounded-full bg-primary-200"></span>
        {{ $sizeTable->title ?: __('store.product.choose_sizes_default') }}
    </p>
    <div class="overflow-x-auto rounded-xl border border-slate-200 shadow-sm">
        <table class="w-full min-w-[520px] border-collapse text-sm">
            <thead>
                <tr class="bg-primary-600 text-white">
                    <th class="text-left font-semibold py-3 px-3 rounded-tl-xl">{{ $sizeTable->title ?: $sizeTable->name }}</th>
                    @foreach($sizeTable->columns as $col)
                        <th class="font-semibold py-3 px-2 text-center">{{ $col->size_value }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                <tr class="bg-slate-100">
                    <td class="font-medium text-slate-700 py-3 px-3">{{ __('store.product.qty_order_row') }}</td>
                    @foreach($sizeTable->columns as $col)
                        <td class="py-2 px-1 text-center">
                            <input type="number" name="{{ $sizeTable->slug }}_size_qty_{{ $col->size_value }}" data-size="{{ $col->size_value }}" data-price-multiplier="{{ number_format((float) ($col->price_multiplier ?? 1), 4, '.', '') }}" min="0" max="999" value="0" class="size-table-input {{ $sizeTable->slug }}-size-input w-full max-w-[72px] mx-auto rounded-lg border border-slate-300 px-2 py-2 text-center text-slate-900 focus:border-primary-500 focus:ring-1 focus:ring-primary-500/30">
                        </td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>
    <div class="mt-3 flex flex-wrap items-center justify-center gap-3 rounded-xl border border-slate-200 bg-gradient-to-r from-slate-50 to-slate-100/80 px-4 py-3 text-sm">
        <span class="inline-flex items-center gap-2 rounded-lg bg-white px-3 py-1.5 shadow-sm ring-1 ring-slate-200/80">
            <span class="text-slate-500 font-medium">{{ __('store.product.size_min_chip') }}</span>
            <span class="font-bold text-slate-800">{{ __('store.product.stock_units_fmt', ['count' => number_format($minOrder)]) }}</span>
        </span>
        <span class="text-slate-300">·</span>
        <span class="inline-flex items-center gap-2 rounded-lg bg-white px-3 py-1.5 shadow-sm ring-1 ring-slate-200/80">
            <span class="text-slate-500 font-medium">{{ __('store.product.size_max_chip') }}</span>
            <span class="font-bold text-slate-800">{{ $availableStock >= 999999 ? __('store.product.unlimited_qty') : __('store.product.stock_units_fmt', ['count' => number_format($availableStock)]) }}</span>
        </span>
        <span class="text-slate-300">·</span>
        <span class="inline-flex items-center gap-2 rounded-lg bg-primary-50 px-3 py-1.5 shadow-sm ring-1 ring-primary-200/80">
            <span class="text-slate-600 font-medium">{{ __('store.product.size_entered_total') }}</span>
            <span id="{{ $sizeTable->slug }}-size-total" class="font-bold text-primary-700">0</span>
            <span class="text-slate-600">{{ __('store.product.units_suffix') }}</span>
        </span>
    </div>
</div>
