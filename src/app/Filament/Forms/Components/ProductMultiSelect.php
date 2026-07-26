<?php

namespace App\Filament\Forms\Components;

use App\Models\Product;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;

/**
 * Çok ürünlü listelerde preload’lu Select yerine aramalı, modern Tom Select UX.
 */
final class ProductMultiSelect
{
    public static function make(string $name = 'product_ids'): Select
    {
        return Select::make($name)
            ->label('Ürünler')
            ->multiple()
            ->searchable()
            ->native(false)
            ->optionsLimit(40)
            ->searchDebounce(300)
            ->placeholder('Ürün adı yazarak ara…')
            ->getSearchResultsUsing(function (string $search): array {
                $search = trim($search);
                if ($search === '') {
                    return Product::query()
                        ->orderBy('name')
                        ->limit(20)
                        ->pluck('name', 'id')
                        ->all();
                }

                return Product::query()
                    ->where(function (Builder $q) use ($search): void {
                        $q->where('name', 'like', '%'.$search.'%');
                        if (ctype_digit($search)) {
                            $q->orWhere('id', (int) $search);
                        }
                    })
                    ->orderBy('name')
                    ->limit(40)
                    ->pluck('name', 'id')
                    ->all();
            })
            ->getOptionLabelsUsing(function (array $values): array {
                if ($values === []) {
                    return [];
                }

                return Product::query()
                    ->whereIn('id', $values)
                    ->orderBy('name')
                    ->pluck('name', 'id')
                    ->all();
            })
            ->helperText('Yazarak arayın, seçilen ürünler etiket olarak listelenir.');
    }

    /** Filament relationship() tabanlı çoklu ürün seçimi (Resource formları). */
    public static function relationship(string $name = 'products'): Select
    {
        return Select::make($name)
            ->label('Ürünler')
            ->relationship(
                name: $name,
                titleAttribute: 'name',
                modifyQueryUsing: fn (Builder $query) => $query->orderBy('name'),
            )
            ->multiple()
            ->searchable()
            ->native(false)
            ->preload(false)
            ->optionsLimit(40)
            ->searchDebounce(300)
            ->placeholder('Ürün adı yazarak ara…')
            ->helperText('Yazarak arayın, seçilen ürünler etiket olarak listelenir.');
    }
}
