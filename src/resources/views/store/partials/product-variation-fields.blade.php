                                                @if($variation->type === 'size_table')
                                                    @php $sizeTableOptionCount = $variation->options->count(); @endphp
                                                    @if($sizeTableOptionCount > 1)
                                                        <p class="text-sm text-slate-600 mb-3">{{ __('store.product.size_table_pick_table_hint') }}</p>
                                                        <div class="flex flex-wrap gap-2.5 sm:gap-3 product-variation-options">
                                                            @foreach($variation->options as $option)
                                                                @php
                                                                    $linkedSizeTable = $option->sizeTable ?? $sizeTablesById->get($option->size_table_id);
                                                                    $sizeTableSlug = $linkedSizeTable?->slug ?? '';
                                                                    $optionDetailText = trim((string) ($option->info_text ?? ''));
                                                                    $optionDetailTitle = $option->option_value;
                                                                    $hasOptionDetail = $optionDetailText !== '';
                                                                @endphp
                                                                @if($linkedSizeTable)
                                                                    <div class="variation-option-wrap relative inline-block shrink-0 overflow-visible">
                                                                    <button type="button"
                                                                        class="product-option border-2 border-slate-300 hover:border-primary-500 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary-500/30 transition-all rounded-xl px-4 py-2.5 sm:px-5 sm:py-3 text-sm sm:text-base font-medium text-slate-700 min-h-[2.75rem]"
                                                                        data-variation="{{ $variation->name }}"
                                                                        data-option="{{ $option->option_value }}"
                                                                        data-option-id="{{ $option->id }}"
                                                                        data-option-solo="1"
                                                                        data-size-table-slug="{{ $sizeTableSlug }}"
                                                                        data-trigger-variation="{{ e($linkedSizeTable->trigger_variation_name ?? '') }}"
                                                                        data-trigger-value="{{ e($linkedSizeTable->trigger_option_value ?? '') }}"
                                                                        data-price-delta="{{ (float) $option->price_delta }}"
                                                                        data-parent-option-id="{{ $option->parent_option_id ?? '' }}"
                                                                        data-parent-option-ids="{{ json_encode($option->getParentOptionIdsList()) }}">
                                                                        {{ $option->option_value }}
                                                                    </button>
                                                                    @include('store.partials.variation-detail-info-btn', [
                                                                        'title' => $optionDetailTitle,
                                                                        'text' => $optionDetailText,
                                                                    ])
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                    <div class="size-table-variation-grids mt-4 {{ $sizeTableOptionCount > 1 ? 'hidden' : '' }}" data-variation-name="{{ $variation->name }}">
                                                        @foreach($variation->options as $option)
                                                            @php $linkedSizeTable = $option->sizeTable ?? $sizeTablesById->get($option->size_table_id); @endphp
                                                            @if($linkedSizeTable)
                                                                @include('store.partials.size-table-grid', [
                                                                    'sizeTable' => $linkedSizeTable,
                                                                    'minOrder' => $minOrder,
                                                                    'availableStock' => $availableStock,
                                                                    'wrapClass' => ($sizeTableOptionCount > 1 ? 'hidden ' : '').'mt-4 first:mt-0 size-table-wrap size-table-wrap-in-variation',
                                                                    'wrapId' => 'size-table-var-'.$variation->id.'-'.$linkedSizeTable->slug,
                                                                    'wrapExtraAttrs' => 'data-size-table-option="'.e($option->option_value).'" data-size-table-slug="'.e($linkedSizeTable->slug).'" data-parent-option-id="'.e($option->parent_option_id ?? '').'" data-parent-option-ids="'.e(json_encode($option->getParentOptionIdsList())).'"',
                                                                ])
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                    @if($sizeTableOptionCount === 0)
                                                        <p class="text-sm text-amber-700">{{ __('store.product.size_table_no_options') }}</p>
                                                    @endif
                                                    <div class="size-table-variation-continue-wrap mt-4 pt-3 border-t border-slate-100 hidden">
                                                        <button type="button" class="size-table-variation-continue-btn w-full py-2.5 sm:py-3 rounded-xl bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold shadow-sm">
                                                            {{ __('store.product.variation_continue') }}
                                                        </button>
                                                    </div>
                                                @else
                                                <div class="@if(in_array($variation->type, ['fabric', 'label_type', 'packaging_type', 'certificate_type', 'delivery_type'], true)) fabric-options-grid grid grid-cols-1 lg:grid-cols-2 gap-2.5 sm:gap-3 @else flex flex-wrap items-start gap-2.5 sm:gap-3 lg:gap-3.5 @endif product-variation-options">
                                    @foreach($variation->options as $option)
                                        @php $optionClasses = 'product-option border-2 border-slate-300 hover:border-primary-500 hover:shadow-md hover:shadow-primary-500/10 focus:outline-none focus:ring-2 focus:ring-primary-500/30 transition-all rounded-xl'; @endphp
                                        @php $parentIdsList = $option->getParentOptionIdsList(); @endphp
                                        @php
                                            $optionDetailText = trim((string) ($option->info_text ?? ''));
                                            $optionDetailTitle = $option->option_value;
                                            $hasOptionDetail = $optionDetailText !== '';
                                            $colorFabricGroupId = ($variation->type === 'color')
                                                ? optional($option->interfaceColorVariation)->interface_fabric_type_variation_id
                                                : null;
                                            $moldSizeTableImageUrl = null;
                                            if ($variation->type === 'mold_model_type') {
                                                $moldSizeTableImageUrl = filled($option->interfaceMoldModelVariation?->size_table_image_path)
                                                    ? \App\Support\MediaUrl::public($option->interfaceMoldModelVariation->size_table_image_path)
                                                    : null;
                                            }
                                        @endphp
                                        @if($variation->type === 'fabric')
                                            @php
                                                $fabricParts = \App\Support\FabricOptionDisplay::parse($option->option_value);
                                                $fabricImageUrl = $option->option_image ? \App\Support\MediaUrl::public($option->option_image) : null;
                                                if ($optionDetailText === '') {
                                                    $optionDetailText = trim((string) ($option->interfaceFabricTypeVariation?->detail_text ?? ''));
                                                }
                                                $hasOptionDetail = $optionDetailText !== '';
                                                $optionDetailTitle = $fabricParts['name'];
                                            @endphp
                                            <div class="variation-option-wrap fabric-option-card-wrap relative w-full min-w-0 overflow-visible">
                                            <button type="button"
                                                class="{{ $optionClasses }} fabric-option-card group w-full text-left flex items-stretch overflow-hidden bg-white hover:bg-slate-50/80 min-h-[4.25rem]"
                                                data-variation="{{ $variation->name }}"
                                                data-option="{{ $option->option_value }}"
                                                data-option-id="{{ $option->id }}"
                                                data-option-solo="{{ $variation->optionValueIsSoloChoice($option->option_value) ? '1' : '0' }}"
                                                data-parent-option-id="{{ $option->parent_option_id ?? '' }}"
                                                data-parent-option-ids="{{ json_encode($parentIdsList) }}"
                                                data-price-delta="{{ (float) $option->price_delta }}"
                                                data-option-image-url="{{ $fabricImageUrl ?? '' }}"
                                                data-fabric-preset-id="{{ $option->interface_fabric_type_variation_id ?? '' }}"
                                                title="{{ $fabricParts['full'] }}">
                                                <span class="fabric-option-accent w-1 shrink-0 bg-slate-200 transition-colors" aria-hidden="true"></span>
                                                <span class="flex flex-1 items-center gap-3 px-3.5 sm:px-4 py-3 min-w-0">
                                                    @if($fabricImageUrl)
                                                        <img src="{{ $fabricImageUrl }}" alt="" class="w-11 h-11 sm:w-12 sm:h-12 rounded-lg object-cover shrink-0 border border-slate-200/80">
                                                    @endif
                                                    <span class="fabric-option-radio shrink-0 w-4 h-4 rounded-full border-2 border-slate-300 bg-white transition-colors" aria-hidden="true"></span>
                                                    <span class="flex-1 min-w-0">
                                                        <span class="flex flex-wrap items-center gap-x-2 gap-y-1">
                                                            @if($fabricParts['yarn_count'])
                                                                <span class="inline-flex items-center rounded-md bg-slate-100 px-2 py-0.5 text-[11px] sm:text-xs font-semibold text-slate-600 tabular-nums">{{ $fabricParts['yarn_count'] }}</span>
                                                            @endif
                                                            <span class="text-sm sm:text-[0.9375rem] font-semibold text-slate-800 leading-snug">{{ $fabricParts['name'] }}</span>
                                                        </span>
                                                        @if($fabricParts['weight'])
                                                            <span class="mt-0.5 block text-xs sm:text-sm text-slate-500 leading-snug">{{ $fabricParts['weight'] }}</span>
                                                        @endif
                                                    </span>
                                                </span>
                                            </button>
                                            @include('store.partials.variation-detail-info-btn', [
                                                'title' => $optionDetailTitle,
                                                'text' => $optionDetailText,
                                            ])
                                            </div>
                                        @elseif($variation->type === 'packaging_type')
                                            @php
                                                $packagingPreset = $option->interfacePackagingPreferenceVariation;
                                                $packagingImageUrl = $option->option_image ? \App\Support\MediaUrl::public($option->option_image) : null;
                                                $packagingRequiresMaterial = (bool) ($packagingPreset?->requires_material ?? false);
                                            @endphp
                                            <div class="variation-option-wrap fabric-option-card-wrap relative w-full min-w-0 overflow-visible">
                                            <button type="button"
                                                class="{{ $optionClasses }} label-option-card group w-full text-left flex items-stretch overflow-hidden bg-white hover:bg-slate-50/80 min-h-[4.25rem]"
                                                data-variation="{{ $variation->name }}"
                                                data-option="{{ $option->option_value }}"
                                                data-option-id="{{ $option->id }}"
                                                data-option-solo="{{ $variation->optionValueIsSoloChoice($option->option_value) ? '1' : '0' }}"
                                                data-parent-option-id="{{ $option->parent_option_id ?? '' }}"
                                                data-parent-option-ids="{{ json_encode($parentIdsList) }}"
                                                data-price-delta="{{ (float) $option->price_delta }}"
                                                data-option-image-url="{{ $packagingImageUrl ?? '' }}"
                                                data-packaging-preset-id="{{ $option->interface_packaging_preference_variation_id ?? '' }}"
                                                data-packaging-slug="{{ $packagingPreset?->slug ?? '' }}"
                                                data-packaging-requires-material="{{ $packagingRequiresMaterial ? '1' : '0' }}"
                                                title="{{ $option->option_value }}">
                                                <span class="label-option-accent w-1 shrink-0 bg-slate-200 transition-colors" aria-hidden="true"></span>
                                                <span class="flex flex-1 items-center gap-3 px-3.5 sm:px-4 py-3 min-w-0">
                                                    @if($packagingImageUrl)
                                                        <img src="{{ $packagingImageUrl }}" alt="" class="w-11 h-11 sm:w-12 sm:h-12 rounded-lg object-cover shrink-0 border border-slate-200/80">
                                                    @endif
                                                    <span class="label-option-radio shrink-0 w-4 h-4 rounded-full border-2 border-slate-300 bg-white transition-colors" aria-hidden="true"></span>
                                                    <span class="flex-1 min-w-0">
                                                        <span class="text-sm sm:text-[0.9375rem] font-semibold text-slate-800 leading-snug">{{ $option->option_value }}</span>
                                                    </span>
                                                </span>
                                            </button>
                                            @include('store.partials.variation-detail-info-btn', [
                                                'title' => $optionDetailTitle,
                                                'text' => $optionDetailText,
                                            ])
                                            </div>
                                        @elseif($variation->type === 'certificate_type')
                                            @php
                                                $certificateImageUrl = $option->option_image ? \App\Support\MediaUrl::public($option->option_image) : null;
                                            @endphp
                                            <div class="variation-option-wrap fabric-option-card-wrap relative w-full min-w-0 overflow-visible">
                                            <button type="button"
                                                class="{{ $optionClasses }} label-option-card group w-full text-left flex items-stretch overflow-hidden bg-white hover:bg-slate-50/80 min-h-[4.25rem]"
                                                data-variation="{{ $variation->name }}"
                                                data-option="{{ $option->option_value }}"
                                                data-option-id="{{ $option->id }}"
                                                data-option-solo="{{ $variation->optionValueIsSoloChoice($option->option_value) ? '1' : '0' }}"
                                                data-parent-option-id="{{ $option->parent_option_id ?? '' }}"
                                                data-parent-option-ids="{{ json_encode($parentIdsList) }}"
                                                data-price-delta="{{ (float) $option->price_delta }}"
                                                data-option-image-url="{{ $certificateImageUrl ?? '' }}"
                                                data-certificate-preset-id="{{ $option->interface_certificate_variation_id ?? '' }}"
                                                title="{{ $option->option_value }}">
                                                <span class="label-option-accent w-1 shrink-0 bg-slate-200 transition-colors" aria-hidden="true"></span>
                                                <span class="flex flex-1 items-center gap-3 px-3.5 sm:px-4 py-3 min-w-0">
                                                    @if($certificateImageUrl)
                                                        <img src="{{ $certificateImageUrl }}" alt="" class="w-11 h-11 sm:w-12 sm:h-12 rounded-lg object-cover shrink-0 border border-slate-200/80">
                                                    @endif
                                                    <span class="label-option-radio shrink-0 w-4 h-4 rounded-full border-2 border-slate-300 bg-white transition-colors" aria-hidden="true"></span>
                                                    <span class="flex-1 min-w-0">
                                                        <span class="text-sm sm:text-[0.9375rem] font-semibold text-slate-800 leading-snug">{{ $option->option_value }}</span>
                                                    </span>
                                                </span>
                                            </button>
                                            @include('store.partials.variation-detail-info-btn', [
                                                'title' => $optionDetailTitle,
                                                'text' => $optionDetailText,
                                            ])
                                            </div>
                                        @elseif($variation->type === 'delivery_type')
                                            @php
                                                $deliveryImageUrl = $option->option_image ? \App\Support\MediaUrl::public($option->option_image) : null;
                                                $deliveryEstimatedTime = trim((string) ($option->interfaceDeliveryMethodVariation?->estimated_delivery_time ?? ''));
                                                if ($optionDetailText === '') {
                                                    $optionDetailText = trim((string) ($option->interfaceDeliveryMethodVariation?->description ?? ''));
                                                }
                                                $hasOptionDetail = $optionDetailText !== '';
                                            @endphp
                                            <div class="variation-option-wrap fabric-option-card-wrap relative w-full min-w-0 overflow-visible">
                                            <button type="button"
                                                class="{{ $optionClasses }} label-option-card group w-full text-left flex items-stretch overflow-hidden bg-white hover:bg-slate-50/80 min-h-[4.25rem]"
                                                data-variation="{{ $variation->name }}"
                                                data-option="{{ $option->option_value }}"
                                                data-option-id="{{ $option->id }}"
                                                data-option-solo="{{ $variation->optionValueIsSoloChoice($option->option_value) ? '1' : '0' }}"
                                                data-parent-option-id="{{ $option->parent_option_id ?? '' }}"
                                                data-parent-option-ids="{{ json_encode($parentIdsList) }}"
                                                data-price-delta="{{ (float) $option->price_delta }}"
                                                data-option-image-url="{{ $deliveryImageUrl ?? '' }}"
                                                data-delivery-preset-id="{{ $option->interface_delivery_method_variation_id ?? '' }}"
                                                data-estimated-delivery-time="{{ e($deliveryEstimatedTime) }}"
                                                title="{{ $option->option_value }}">
                                                <span class="label-option-accent w-1 shrink-0 bg-slate-200 transition-colors" aria-hidden="true"></span>
                                                <span class="flex flex-1 items-center gap-3 px-3.5 sm:px-4 py-3 min-w-0">
                                                    @if($deliveryImageUrl)
                                                        <img src="{{ $deliveryImageUrl }}" alt="" class="w-11 h-11 sm:w-12 sm:h-12 rounded-lg object-cover shrink-0 border border-slate-200/80">
                                                    @endif
                                                    <span class="label-option-radio shrink-0 w-4 h-4 rounded-full border-2 border-slate-300 bg-white transition-colors" aria-hidden="true"></span>
                                                    <span class="flex-1 min-w-0">
                                                        <span class="text-sm sm:text-[0.9375rem] font-semibold text-slate-800 leading-snug">{{ $option->option_value }}</span>
                                                    </span>
                                                </span>
                                            </button>
                                            @include('store.partials.variation-detail-info-btn', [
                                                'title' => $optionDetailTitle,
                                                'text' => $optionDetailText,
                                            ])
                                            </div>
                                        @elseif($variation->type === 'label_type')
                                            @php
                                                $labelPreset = $option->interfaceLabelTypeVariation;
                                                $labelImageUrl = $option->option_image ? \App\Support\MediaUrl::public($option->option_image) : null;
                                                $labelPositions = array_filter([
                                                    ($labelPreset?->position_front ?? false) ? __('store.product.label_position_front') : null,
                                                    ($labelPreset?->position_back ?? false) ? __('store.product.label_position_back') : null,
                                                ]);
                                            @endphp
                                            <div class="variation-option-wrap fabric-option-card-wrap relative w-full min-w-0 overflow-visible">
                                            <button type="button"
                                                class="{{ $optionClasses }} label-option-card group w-full text-left flex items-stretch overflow-hidden bg-white hover:bg-slate-50/80 min-h-[4.25rem]"
                                                data-variation="{{ $variation->name }}"
                                                data-option="{{ $option->option_value }}"
                                                data-option-id="{{ $option->id }}"
                                                data-option-solo="{{ $variation->optionValueIsSoloChoice($option->option_value) ? '1' : '0' }}"
                                                data-parent-option-id="{{ $option->parent_option_id ?? '' }}"
                                                data-parent-option-ids="{{ json_encode($parentIdsList) }}"
                                                data-price-delta="{{ (float) $option->price_delta }}"
                                                data-option-image-url="{{ $labelImageUrl ?? '' }}"
                                                data-label-preset-id="{{ $option->interface_label_type_variation_id ?? '' }}"
                                                data-label-custom-print="{{ ($labelPreset?->is_custom_print ?? false) ? '1' : '0' }}"
                                                data-label-position-front="{{ ($labelPreset?->position_front ?? false) ? '1' : '0' }}"
                                                data-label-position-back="{{ ($labelPreset?->position_back ?? false) ? '1' : '0' }}"
                                                data-label-ask-description="{{ ($labelPreset?->ask_description ?? false) ? '1' : '0' }}"
                                                data-label-description-title="{{ e($labelPreset?->description_title ?? '') }}"
                                                title="{{ $option->option_value }}">
                                                <span class="label-option-accent w-1 shrink-0 bg-slate-200 transition-colors" aria-hidden="true"></span>
                                                <span class="flex flex-1 items-center gap-3 px-3.5 sm:px-4 py-3 min-w-0">
                                                    @if($labelImageUrl)
                                                        <img src="{{ $labelImageUrl }}" alt="" class="w-11 h-11 sm:w-12 sm:h-12 rounded-lg object-cover shrink-0 border border-slate-200/80">
                                                    @endif
                                                    <span class="label-option-radio shrink-0 w-4 h-4 rounded-full border-2 border-slate-300 bg-white transition-colors" aria-hidden="true"></span>
                                                    <span class="flex-1 min-w-0">
                                                        <span class="text-sm sm:text-[0.9375rem] font-semibold text-slate-800 leading-snug">{{ $option->option_value }}</span>
                                                        <span class="mt-1 flex flex-wrap gap-1.5">
                                                            @if($labelPreset?->is_custom_print)
                                                                <span class="inline-flex items-center rounded-md bg-amber-50 px-2 py-0.5 text-[11px] font-medium text-amber-800 ring-1 ring-amber-200/80">{{ __('store.product.label_custom_print') }}</span>
                                                            @endif
                                                            @foreach($labelPositions as $pos)
                                                                <span class="inline-flex items-center rounded-md bg-slate-100 px-2 py-0.5 text-[11px] font-medium text-slate-600">{{ $pos }}</span>
                                                            @endforeach
                                                        </span>
                                                    </span>
                                                </span>
                                            </button>
                                            @include('store.partials.variation-detail-info-btn', [
                                                'title' => $optionDetailTitle,
                                                'text' => $optionDetailText,
                                            ])
                                            </div>
                                        @elseif($variation->type === 'mold_model_type' && $option->option_image)
                                            @php
                                                $optionImageUrl = \App\Support\MediaUrl::public($option->option_image);
                                                $imgSize = $option->option_image_size ?? 'medium';
                                                $imgSizeClass = match($imgSize) { 'small' => 'w-14 h-14 sm:w-16 sm:h-16', 'large' => 'w-28 h-28 sm:w-32 sm:h-32', default => 'w-20 h-20 sm:w-24 sm:h-24' };
                                                $minWClass = match($imgSize) { 'small' => 'min-w-[72px] sm:min-w-[88px]', 'large' => 'min-w-[120px] sm:min-w-[140px]', default => 'min-w-[88px] sm:min-w-[110px]' };
                                                $labelMaxW = match($imgSize) { 'small' => 'max-w-[80px] sm:max-w-[88px]', 'large' => 'max-w-[120px] sm:max-w-[140px]', default => 'max-w-[96px] sm:max-w-[110px]' };
                                            @endphp
                                            <div class="variation-option-wrap relative inline-block shrink-0 overflow-visible">
                                            <div class="mold-model-option-shell flex flex-col overflow-hidden rounded-xl border-2 border-slate-300 bg-white transition-all hover:border-primary-500 hover:shadow-md hover:shadow-primary-500/10 {{ $minWClass }}">
                                            <button type="button" class="product-option flex w-full flex-col items-center rounded-none border-0 bg-transparent p-2 sm:p-3 shadow-none hover:bg-slate-50/50 hover:shadow-none focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500/30" data-variation="{{ $variation->name }}" data-option="{{ $option->option_value }}" data-option-id="{{ $option->id }}" data-option-solo="{{ $variation->optionValueIsSoloChoice($option->option_value) ? '1' : '0' }}" data-parent-option-id="{{ $option->parent_option_id ?? '' }}" data-parent-option-ids="{{ json_encode($parentIdsList) }}" data-price-delta="{{ (float) $option->price_delta }}" data-option-image-url="{{ $optionImageUrl }}" data-mold-model-preset-id="{{ $option->interface_mold_model_variation_id ?? '' }}">
                                                <span class="relative inline-block group">
                                                    <img src="{{ $optionImageUrl }}" alt="{{ $option->option_value }}" class="{{ $imgSizeClass }} object-cover rounded-xl">
                                                    <span class="variation-zoom-btn absolute top-1 right-1 w-6 h-6 rounded-md bg-black/50 hover:bg-primary-600 flex items-center justify-center text-white cursor-pointer transition-all opacity-0 group-hover:opacity-100" data-image-url="{{ $optionImageUrl }}" data-image-alt="{{ $option->option_value }}" title="{{ __('store.product.variation_zoom') }}" role="button" aria-label="{{ $option->option_value }}{{ __('store.product.variation_zoom_aria_suffix') }}">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                                                    </span>
                                                </span>
                                                <span class="text-sm font-medium text-slate-600 mt-2 w-full {{ $labelMaxW }} text-center break-words leading-tight">{{ $option->option_value }}</span>
                                            </button>
                                            @include('store.partials.mold-model-size-table-view-btn', [
                                                'sizeTableImageUrl' => $moldSizeTableImageUrl,
                                                'optionTitle' => $option->option_value,
                                            ])
                                            </div>
                                            @include('store.partials.variation-detail-info-btn', [
                                                'title' => $optionDetailTitle,
                                                'text' => $optionDetailText,
                                            ])
                                            </div>
                                        @elseif(in_array($variation->type, ['image', 'color'], true) && $option->option_image)
                                            @php $optionImageUrl = \App\Support\MediaUrl::public($option->option_image); $imgSize = $option->option_image_size ?? 'medium'; $imgSizeClass = match($imgSize) { 'small' => 'w-14 h-14 sm:w-16 sm:h-16', 'large' => 'w-28 h-28 sm:w-32 sm:h-32', default => 'w-20 h-20 sm:w-24 sm:h-24' }; $minWClass = match($imgSize) { 'small' => 'min-w-[72px] sm:min-w-[88px]', 'large' => 'min-w-[120px] sm:min-w-[140px]', default => 'min-w-[88px] sm:min-w-[110px]' }; $labelMaxW = match($imgSize) { 'small' => 'max-w-[80px] sm:max-w-[88px]', 'large' => 'max-w-[120px] sm:max-w-[140px]', default => 'max-w-[96px] sm:max-w-[110px]' }; @endphp
                                            <div class="variation-option-wrap relative inline-block shrink-0 overflow-visible">
                                            <button type="button" class="{{ $optionClasses }} flex flex-col items-center rounded-xl p-2 sm:p-3 {{ $minWClass }}" data-variation="{{ $variation->name }}" data-option="{{ $option->option_value }}" data-option-id="{{ $option->id }}" data-option-solo="{{ $variation->optionValueIsSoloChoice($option->option_value) ? '1' : '0' }}" data-parent-option-id="{{ $option->parent_option_id ?? '' }}" data-parent-option-ids="{{ json_encode($parentIdsList) }}" data-price-delta="{{ (float) $option->price_delta }}" data-option-image-url="{{ $optionImageUrl }}" @if($variation->type === 'color') data-color-fabric-group-id="{{ $colorFabricGroupId ?? '' }}" @endif>
                                                <span class="relative inline-block group">
                                                    <img src="{{ $optionImageUrl }}" alt="{{ $option->option_value }}" class="{{ $imgSizeClass }} object-cover rounded-xl">
                                                    <span class="variation-zoom-btn absolute top-1 right-1 w-6 h-6 rounded-md bg-black/50 hover:bg-primary-600 flex items-center justify-center text-white cursor-pointer transition-all opacity-0 group-hover:opacity-100" data-image-url="{{ $optionImageUrl }}" data-image-alt="{{ $option->option_value }}" title="{{ __('store.product.variation_zoom') }}" role="button" aria-label="{{ $option->option_value }}{{ __('store.product.variation_zoom_aria_suffix') }}">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                                                    </span>
                                                </span>
                                                <span class="text-sm font-medium text-slate-600 mt-2 w-full {{ $labelMaxW }} text-center break-words leading-tight">{{ $option->option_value }}</span>
                                            </button>
                                            @include('store.partials.variation-detail-info-btn', [
                                                'title' => $optionDetailTitle,
                                                'text' => $optionDetailText,
                                            ])
                                            </div>
                                        @elseif($variation->type === 'color' && $option->option_color)
                                            <div class="variation-option-wrap relative inline-block shrink-0 overflow-visible">
                                            <button type="button" class="{{ $optionClasses }} flex flex-col items-center rounded-xl p-1.5 shrink-0 min-w-[72px]" data-variation="{{ $variation->name }}" data-option="{{ $option->option_value }}" data-option-id="{{ $option->id }}" data-option-solo="{{ $variation->optionValueIsSoloChoice($option->option_value) ? '1' : '0' }}" data-parent-option-id="{{ $option->parent_option_id ?? '' }}" data-parent-option-ids="{{ json_encode($parentIdsList) }}" data-price-delta="{{ (float) $option->price_delta }}" data-option-image-url="{{ $option->option_image ? \App\Support\MediaUrl::public($option->option_image) : '' }}" data-color-fabric-group-id="{{ $colorFabricGroupId ?? '' }}" title="{{ $option->option_value }}">
                                                <span class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg shrink-0 border border-slate-200" style="background-color: {{ $option->option_color }}"></span>
                                                <span class="text-xs font-medium text-slate-600 mt-1.5 w-full max-w-[88px] sm:max-w-[100px] text-center break-words leading-tight">{{ $option->option_value }}</span>
                                            </button>
                                            @include('store.partials.variation-detail-info-btn', [
                                                'title' => $optionDetailTitle,
                                                'text' => $optionDetailText,
                                            ])
                                            </div>
                                        @elseif($variation->type === 'mold_model_type')
                                            <div class="variation-option-wrap relative inline-block shrink-0 overflow-visible">
                                            <div class="mold-model-option-shell flex min-w-[7.5rem] flex-col overflow-hidden rounded-xl border-2 border-slate-300 bg-white transition-all hover:border-primary-500 hover:shadow-md hover:shadow-primary-500/10 sm:min-w-[8.5rem]">
                                            <button type="button" class="product-option w-full rounded-none border-0 bg-transparent px-4 py-2.5 text-sm font-medium text-slate-700 shadow-none hover:bg-slate-50/50 hover:shadow-none focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500/30 sm:px-5 sm:py-3 sm:text-base min-h-[2.75rem]" data-variation="{{ $variation->name }}" data-option="{{ $option->option_value }}" data-option-id="{{ $option->id }}" data-option-solo="{{ $variation->optionValueIsSoloChoice($option->option_value) ? '1' : '0' }}" data-parent-option-id="{{ $option->parent_option_id ?? '' }}" data-parent-option-ids="{{ json_encode($parentIdsList) }}" data-price-delta="{{ (float) $option->price_delta }}" data-option-image-url="{{ $option->option_image ? \App\Support\MediaUrl::public($option->option_image) : '' }}" data-mold-model-preset-id="{{ $option->interface_mold_model_variation_id ?? '' }}">{{ $option->option_value }}</button>
                                            @include('store.partials.mold-model-size-table-view-btn', [
                                                'sizeTableImageUrl' => $moldSizeTableImageUrl,
                                                'optionTitle' => $option->option_value,
                                            ])
                                            </div>
                                            @include('store.partials.variation-detail-info-btn', [
                                                'title' => $optionDetailTitle,
                                                'text' => $optionDetailText,
                                            ])
                                            </div>
                                        @else
                                            <div class="variation-option-wrap relative inline-block shrink-0 overflow-visible">
                                            <button type="button" class="{{ $optionClasses }} px-4 py-2.5 sm:px-5 sm:py-3 text-sm sm:text-base font-medium text-slate-700 min-h-[2.75rem]" data-variation="{{ $variation->name }}" data-option="{{ $option->option_value }}" data-option-id="{{ $option->id }}" data-option-solo="{{ $variation->optionValueIsSoloChoice($option->option_value) ? '1' : '0' }}" data-parent-option-id="{{ $option->parent_option_id ?? '' }}" data-parent-option-ids="{{ json_encode($parentIdsList) }}" data-price-delta="{{ (float) $option->price_delta }}" data-option-image-url="{{ $option->option_image ? \App\Support\MediaUrl::public($option->option_image) : '' }}" @if($variation->type === 'color') data-color-fabric-group-id="{{ $colorFabricGroupId ?? '' }}" @endif>{{ $option->option_value }}</button>
                                            @include('store.partials.variation-detail-info-btn', [
                                                'title' => $optionDetailTitle,
                                                'text' => $optionDetailText,
                                            ])
                                            </div>
                                        @endif
                                    @endforeach
                                                </div>
                                                @if($variation->type === 'label_type')
                                                    <div class="label-type-suboptions-wrap mt-5 sm:mt-6 hidden">
                                                        <div class="label-type-suboptions-panel rounded-2xl border-2 border-primary-300/80 bg-gradient-to-br from-primary-50 via-white to-sky-50/40 p-4 sm:p-5 shadow-lg shadow-primary-900/5 ring-1 ring-primary-200/60">
                                                            <div class="label-type-suboptions-panel-header flex items-start gap-3 sm:gap-4 mb-4 sm:mb-5 pb-4 border-b border-primary-200/70">
                                                                <span class="flex h-11 w-11 sm:h-12 sm:w-12 shrink-0 items-center justify-center rounded-xl bg-primary-600 text-white shadow-md" aria-hidden="true">
                                                                    <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/></svg>
                                                                </span>
                                                                <div class="min-w-0 flex-1">
                                                                    <p class="label-type-suboptions-heading text-base sm:text-lg font-bold text-slate-900 leading-snug"></p>
                                                                    <p class="mt-1.5 text-sm sm:text-[0.9375rem] text-slate-600 leading-relaxed">{{ __('store.product.label_suboptions_panel_hint') }}</p>
                                                                </div>
                                                            </div>
                                                            <div class="label-type-suboptions-body space-y-3.5 sm:space-y-4">
                                                                <div class="label-type-sub-section label-type-custom-print-section hidden rounded-xl border border-slate-200/90 bg-white/95 p-3.5 sm:p-4 shadow-sm">
                                                                    <p class="text-sm sm:text-base font-bold text-slate-900 mb-3">{{ __('store.product.label_custom_print_question') }}</p>
                                                                    <div class="flex flex-col sm:flex-row sm:flex-wrap sm:items-stretch gap-2.5">
                                                                        <button type="button" class="label-type-custom-print-btn w-full sm:w-auto px-4 py-3 rounded-xl border-2 border-slate-300 text-sm sm:text-base font-semibold text-slate-700 hover:bg-slate-50 min-h-[3rem]" data-value="1">{{ __('store.product.label_custom_print_yes') }}</button>
                                                                        <button type="button" class="label-type-custom-print-btn w-full sm:w-auto px-4 py-3 rounded-xl border-2 border-slate-300 text-sm sm:text-base font-semibold text-slate-700 hover:bg-slate-50 min-h-[3rem]" data-value="0">{{ __('store.product.label_custom_print_no') }}</button>
                                                                    </div>
                                                                    <div class="label-type-standard-wash-info hidden mt-3 sm:mt-4 rounded-xl border-2 border-sky-400/80 bg-gradient-to-br from-sky-50 via-sky-50/95 to-blue-50 p-4 sm:p-5 shadow-md ring-2 ring-sky-200/70" role="status" aria-live="polite">
                                                                        <div class="flex items-start gap-3 sm:gap-4">
                                                                            <span class="flex h-11 w-11 sm:h-12 sm:w-12 shrink-0 items-center justify-center rounded-full bg-sky-500 text-white shadow-sm" aria-hidden="true">
                                                                                <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                                            </span>
                                                                            <div class="min-w-0 flex-1">
                                                                                <p class="text-sm sm:text-base font-bold text-sky-950 leading-snug">{{ __('store.product.label_custom_print_no_standard_info') }}</p>
                                                                                <p class="mt-1.5 text-sm sm:text-[0.9375rem] text-sky-900/90 leading-relaxed">{{ __('store.product.label_custom_print_no_standard_body') }}</p>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="label-type-sub-section label-type-custom-print-artwork-section hidden rounded-xl border border-slate-200/90 bg-white/95 p-3.5 sm:p-4 shadow-sm">
                                                                    <p class="text-sm sm:text-base font-bold text-slate-900 mb-3">{{ __('store.product.label_custom_print_artwork_question') }}</p>
                                                                    <div class="flex flex-col sm:flex-row sm:flex-wrap gap-2.5">
                                                                        <button type="button" class="label-type-custom-print-artwork-btn w-full sm:flex-1 px-4 py-3 rounded-xl border-2 border-slate-300 text-sm sm:text-base font-semibold text-slate-700 hover:bg-slate-50 min-h-[3rem]" data-artwork="customer_send">{{ __('store.product.label_custom_print_artwork_customer') }}</button>
                                                                        <button type="button" class="label-type-custom-print-artwork-btn w-full sm:flex-1 px-4 py-3 rounded-xl border-2 border-slate-300 text-sm sm:text-base font-semibold text-slate-700 hover:bg-slate-50 min-h-[3rem]" data-artwork="company_prepare">{{ __('store.product.label_custom_print_artwork_company') }}</button>
                                                                    </div>
                                                                </div>
                                                                <div class="label-type-sub-section label-type-position-section hidden rounded-xl border border-slate-200/90 bg-white/95 p-3.5 sm:p-4 shadow-sm">
                                                                    <p class="text-sm sm:text-base font-bold text-slate-900 mb-3">{{ __('store.product.label_position_pick') }}</p>
                                                                    <div class="flex flex-col sm:flex-row gap-2.5">
                                                                        <button type="button" class="label-type-position-btn hidden w-full sm:w-auto px-4 py-3 rounded-xl border-2 border-slate-300 text-sm sm:text-base font-semibold text-slate-700 hover:bg-slate-50 min-h-[3rem]" data-position="front">{{ __('store.product.label_position_front') }}</button>
                                                                        <button type="button" class="label-type-position-btn hidden w-full sm:w-auto px-4 py-3 rounded-xl border-2 border-slate-300 text-sm sm:text-base font-semibold text-slate-700 hover:bg-slate-50 min-h-[3rem]" data-position="back">{{ __('store.product.label_position_back') }}</button>
                                                                    </div>
                                                                </div>
                                                                <div class="label-type-sub-section label-type-description-section hidden rounded-xl border border-slate-200/90 bg-white/95 p-3.5 sm:p-4 shadow-sm">
                                                                    <label class="label-type-description-heading text-sm sm:text-base font-bold text-slate-900 mb-3 block"></label>
                                                                    <textarea rows="3" maxlength="500" class="label-type-description-input w-full rounded-xl border-2 border-slate-300/90 bg-white px-3.5 py-3 text-sm sm:text-base text-slate-800 shadow-sm transition-colors placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/25" placeholder="{{ __('store.product.label_description_placeholder') }}"></textarea>
                                                                </div>
                                                            </div>
                                                            <button type="button" class="label-type-continue-btn mt-4 sm:mt-5 w-full py-3 sm:py-3.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white text-sm sm:text-base font-bold shadow-sm disabled:opacity-50 disabled:cursor-not-allowed transition-colors" disabled>
                                                                {{ __('store.product.variation_continue') }}
                                                            </button>
                                                        </div>
                                                    </div>
                                                @elseif($variation->type === 'packaging_type')
                                                    @php
                                                        $packagingCatalogData = $packagingCatalog ?? ['materials' => [], 'customizations' => [], 'barcode' => ['enabled' => false]];
                                                        $packagingMaterials = $packagingCatalogData['materials'] ?? [];
                                                        $packagingCustomizations = $packagingCatalogData['customizations'] ?? [];
                                                        $packagingBarcode = $packagingCatalogData['barcode'] ?? [];
                                                    @endphp
                                                    <div class="packaging-type-suboptions-wrap mt-4 pt-3 border-t border-slate-100 hidden">
                                                        <div class="packaging-type-material-section hidden mb-4">
                                                            <p class="text-sm font-semibold text-slate-800 mb-2">{{ __('store.product.packaging_material_pick') }}</p>
                                                            <div class="flex flex-wrap gap-2.5">
                                                                @foreach($packagingMaterials as $material)
                                                                    <button type="button"
                                                                        class="packaging-type-material-btn px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-medium text-slate-700 hover:bg-slate-50 min-h-[2.75rem]"
                                                                        data-material-slug="{{ $material['slug'] ?? '' }}"
                                                                        data-material-name="{{ $material['name'] ?? '' }}">
                                                                        {{ $material['name'] ?? '' }}
                                                                    </button>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                        <div class="packaging-type-customization-section mb-4">
                                                            <p class="text-sm font-semibold text-slate-800 mb-2">{{ __('store.product.packaging_customization_pick') }}</p>
                                                            <div class="flex flex-col gap-2.5">
                                                                @foreach($packagingCustomizations as $customization)
                                                                    @php
                                                                        $extraPrice = (float) ($customization['extra_price'] ?? 0);
                                                                        $extraLabel = $extraPrice > 0
                                                                            ? __('store.product.packaging_extra_price_fmt', ['price' => number_format($extraPrice, 2, ',', '.')])
                                                                            : __('store.product.packaging_no_extra_price');
                                                                    @endphp
                                                                    <button type="button"
                                                                        class="packaging-type-customization-btn w-full text-left px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-medium text-slate-700 hover:bg-slate-50 min-h-[2.75rem] flex items-center justify-between gap-3"
                                                                        data-customization-slug="{{ $customization['slug'] ?? '' }}"
                                                                        data-customization-name="{{ $customization['name'] ?? '' }}"
                                                                        data-extra-price="{{ $extraPrice }}"
                                                                        data-is-default="{{ !empty($customization['is_default']) ? '1' : '0' }}">
                                                                        <span>{{ $customization['name'] ?? '' }}</span>
                                                                        <span class="text-xs text-slate-500 shrink-0">{{ $extraLabel }}</span>
                                                                    </button>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                        @if(!empty($packagingBarcode['enabled']))
                                                            <div class="packaging-type-barcode-section mb-4 rounded-xl border border-slate-200 bg-slate-50/80 p-3.5 sm:p-4">
                                                                <label class="flex items-start gap-3 cursor-pointer">
                                                                    <input type="checkbox" class="packaging-type-barcode-check mt-1 rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                                                                    <span class="min-w-0">
                                                                        <span class="block text-sm font-semibold text-slate-800">{{ $packagingBarcode['label'] ?? __('store.product.packaging_barcode_label') }}</span>
                                                                        @if(!empty($packagingBarcode['description']))
                                                                            <span class="mt-1 block text-xs text-slate-500 leading-relaxed">{{ $packagingBarcode['description'] }}</span>
                                                                        @endif
                                                                        @if((float) ($packagingBarcode['extra_price'] ?? 0) > 0)
                                                                            <span class="mt-1 block text-xs text-slate-600">{{ __('store.product.packaging_extra_price_fmt', ['price' => number_format((float) $packagingBarcode['extra_price'], 2, ',', '.')]) }}</span>
                                                                        @endif
                                                                    </span>
                                                                </label>
                                                                @if(!empty($packagingBarcode['image_url']))
                                                                    <img src="{{ $packagingBarcode['image_url'] }}" alt="" class="mt-3 max-h-32 rounded-lg border border-slate-200 object-contain">
                                                                @endif
                                                                <div class="packaging-type-sticker-design-section hidden mt-4 border-t border-primary-200/70 pt-4">
                                                                    <p class="text-sm sm:text-base font-bold text-slate-900">{{ __('store.product.packaging_sticker_design_question') }}</p>
                                                                    <p class="mt-1 mb-3 text-xs sm:text-sm text-slate-600 leading-relaxed">{{ __('store.product.packaging_sticker_design_hint') }}</p>
                                                                    <div class="flex flex-col gap-2.5">
                                                                        <button type="button"
                                                                            class="packaging-type-sticker-design-btn w-full text-left px-4 py-3 rounded-xl border-2 border-slate-300 text-sm sm:text-base font-semibold text-slate-700 hover:bg-white min-h-[3rem]"
                                                                            data-sticker-design="customer_send"
                                                                            data-sticker-label="{{ __('store.product.packaging_sticker_design_summary_customer') }}">
                                                                            {{ __('store.product.packaging_sticker_design_customer') }}
                                                                        </button>
                                                                        <button type="button"
                                                                            class="packaging-type-sticker-design-btn w-full text-left px-4 py-3 rounded-xl border-2 border-slate-300 text-sm sm:text-base font-semibold text-slate-700 hover:bg-white min-h-[3rem]"
                                                                            data-sticker-design="company_prepare"
                                                                            data-sticker-label="{{ __('store.product.packaging_sticker_design_summary_company') }}">
                                                                            {{ __('store.product.packaging_sticker_design_company') }}
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        <button type="button" class="packaging-type-continue-btn w-full py-2.5 sm:py-3 rounded-xl bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold shadow-sm disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                                                            {{ __('store.product.variation_continue') }}
                                                        </button>
                                                    </div>
                                                @elseif($variation->type === 'delivery_type')
                                                    <div class="delivery-type-suboptions-wrap mt-5 sm:mt-6 hidden">
                                                        <div class="delivery-type-suboptions-panel rounded-2xl border-2 border-primary-300/80 bg-gradient-to-br from-primary-50 via-white to-sky-50/40 p-4 sm:p-5 shadow-lg shadow-primary-900/5 ring-1 ring-primary-200/60">
                                                            <div class="flex items-start gap-3 sm:gap-4 mb-4 sm:mb-5 pb-4 border-b border-primary-200/70">
                                                                <span class="flex h-11 w-11 sm:h-12 sm:w-12 shrink-0 items-center justify-center rounded-xl bg-primary-600 text-white shadow-md" aria-hidden="true">
                                                                    <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                                                </span>
                                                                <div class="min-w-0 flex-1">
                                                                    <p class="delivery-type-suboptions-heading text-base sm:text-lg font-bold text-slate-900 leading-snug"></p>
                                                                    <p class="mt-1.5 text-sm sm:text-[0.9375rem] text-slate-600 leading-relaxed">{{ __('store.product.delivery_suboptions_panel_hint') }}</p>
                                                                    <p class="delivery-type-estimated-time hidden mt-3 rounded-lg border border-sky-300/80 bg-sky-50 px-3 py-2.5 text-sm font-semibold text-sky-900"></p>
                                                                </div>
                                                            </div>
                                                            <p class="text-sm sm:text-base font-bold text-slate-900 mb-3">{{ __('store.product.delivery_suboption_pick') }}</p>
                                                            <div class="delivery-type-suboptions-list flex flex-col gap-2.5"></div>
                                                            <div class="delivery-type-suboption-info hidden mt-3 sm:mt-4 rounded-xl border-2 border-sky-400/80 bg-gradient-to-br from-sky-50 via-sky-50/95 to-blue-50 p-4 sm:p-5 shadow-md ring-2 ring-sky-200/70" role="status" aria-live="polite">
                                                                <p class="delivery-type-suboption-info-text text-sm sm:text-[0.9375rem] text-sky-900/90 leading-relaxed whitespace-pre-line"></p>
                                                            </div>
                                                            <button type="button" class="delivery-type-continue-btn mt-4 sm:mt-5 w-full py-3 sm:py-3.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white text-sm sm:text-base font-bold shadow-sm disabled:opacity-50 disabled:cursor-not-allowed transition-colors" disabled>
                                                                {{ __('store.product.variation_continue') }}
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endif
                                                @endif
