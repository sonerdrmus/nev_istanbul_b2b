@php
    $containers = $getChildComponentContainers();
    $addAction = $getAction($getAddActionName());
    $deleteAction = $getAction($getDeleteActionName());
    $reorderAction = $getAction($getReorderActionName());
    $isAddable = $isAddable();
    $isDeletable = $isDeletable();
    $isReorderableWithDragAndDrop = $isReorderableWithDragAndDrop();
    $statePath = $getStatePath();
    $tableHeaders = $tableHeaders ?? [];
    $emptyMessage = $emptyMessage ?? 'Henüz satır yok. Aşağıdan satır ekleyin.';
    $tableMinWidth = $tableMinWidth ?? '40rem';
    $colspan = ($isReorderableWithDragAndDrop ? 1 : 0) + count($tableHeaders) + ($isDeletable ? 1 : 0);
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div
        {{
            $attributes
                ->merge($getExtraAttributes(), escape: false)
                ->class(['fi-fo-form-table-repeater'])
        }}
    >
        <div class="form-table-repeater-scroll rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <table class="w-full divide-y divide-gray-200 text-sm dark:divide-gray-700" style="min-width: {{ $tableMinWidth }}">
                <thead class="bg-gray-50 dark:bg-gray-800/80">
                    <tr>
                        @if ($isReorderableWithDragAndDrop)
                            <th scope="col" class="w-10 px-2 py-3"></th>
                        @endif
                        @foreach ($tableHeaders as $header)
                            <th
                                scope="col"
                                @class([
                                    'px-3 py-3 text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300',
                                    'text-center w-20' => ($header['align'] ?? 'left') === 'center',
                                    'text-left' => ($header['align'] ?? 'left') !== 'center',
                                ])
                            >
                                {{ $header['label'] ?? $header }}
                            </th>
                        @endforeach
                        @if ($isDeletable)
                            <th scope="col" class="w-12 px-2 py-3"></th>
                        @endif
                    </tr>
                </thead>
                <tbody
                    class="divide-y divide-gray-100 dark:divide-gray-800"
                    @if ($isReorderableWithDragAndDrop)
                        wire:end.stop="mountFormComponentAction('{{ $statePath }}', 'reorder', { items: $event.target.sortable.toArray() })"
                        x-sortable
                        data-sortable-animation-duration="{{ $getReorderAnimationDuration() }}"
                    @endif
                >
                    @forelse ($containers as $uuid => $item)
                        @php
                            $deleteActionItem = $deleteAction(['item' => $uuid]);
                            $deleteActionIsVisible = $isDeletable && $deleteActionItem->isVisible();
                            $reorderActionIsVisible = $isReorderableWithDragAndDrop && $reorderAction->isVisible();
                            $rowComponents = collect($item->getComponents(withHidden: true));
                            $hiddenComponents = $rowComponents->filter(
                                fn ($component): bool => $component instanceof \Filament\Forms\Components\Hidden,
                            );
                            $visibleComponents = $rowComponents->reject(
                                fn ($component): bool => $component instanceof \Filament\Forms\Components\Hidden,
                            );
                        @endphp
                        <tr
                            wire:key="{{ $this->getId() }}.{{ $item->getStatePath() }}.{{ $field::class }}.item"
                            @if ($isReorderableWithDragAndDrop)
                                x-sortable-item="{{ $uuid }}"
                            @endif
                            class="fi-fo-repeater-item bg-white align-top dark:bg-gray-900"
                        >
                            @foreach ($hiddenComponents as $hiddenComponent)
                                {{ $hiddenComponent }}
                            @endforeach
                            @if ($isReorderableWithDragAndDrop)
                                <td class="px-2 py-2 text-center">
                                    @if ($reorderActionIsVisible)
                                        <div x-sortable-handle class="inline-flex cursor-grab text-gray-400 hover:text-gray-600">
                                            {{ $reorderAction }}
                                        </div>
                                    @endif
                                </td>
                            @endif
                            @foreach ($visibleComponents as $component)
                                <td class="form-table-repeater-cell px-3 py-2 [&_.fi-fo-field]:!gap-1 [&_label]:sr-only">
                                    {{ $component }}
                                </td>
                            @endforeach
                            @if ($isDeletable)
                                <td class="px-2 py-2 text-center">
                                    @if ($deleteActionIsVisible)
                                        {{ $deleteActionItem }}
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $colspan }}" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                {{ $emptyMessage }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($isAddable)
            <div class="mt-3">
                {{ $addAction }}
            </div>
        @endif
    </div>
</x-dynamic-component>
