@props(['title' => '', 'text' => '', 'inline' => false])

@php
    $detailText = trim((string) ($text ?? ''));
    $detailTitle = (string) ($title ?? '');
@endphp

@if($detailText !== '')
    <button type="button"
        class="variation-detail-info-btn fabric-detail-info-btn inline-flex items-center gap-1 whitespace-nowrap shrink-0 {{ $inline ? '' : 'absolute -top-2 -right-2 z-20' }} rounded-md bg-white px-1.5 py-1 text-[10px] sm:text-[11px] font-semibold leading-none text-sky-700 shadow-md ring-1 ring-sky-200/90 hover:bg-sky-50 hover:text-sky-900 focus:outline-none focus:ring-2 focus:ring-sky-400/40"
        data-detail-title="{{ e($detailTitle) }}"
        data-detail-text="{{ e($detailText) }}"
        data-fabric-detail-title="{{ e($detailTitle) }}"
        data-fabric-detail-text="{{ e($detailText) }}"
        aria-label="{{ __('store.product.fabric_detail_info') }}: {{ $detailTitle }}">
        <svg class="h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span class="whitespace-nowrap">{{ __('store.product.fabric_detail_info') }}</span>
    </button>
@endif
