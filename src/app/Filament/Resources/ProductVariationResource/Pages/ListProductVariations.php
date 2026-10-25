<?php

namespace App\Filament\Resources\ProductVariationResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Filament\Resources\ProductVariationResource;
use Filament\Resources\Pages\ListRecords;

/** Varyasyonların ayrı bir listesi yok; ürün listesine yönlendirir (Filament'in index rotası için). */
class ListProductVariations extends ListRecords
{
    protected static string $resource = ProductVariationResource::class;

    public function mount(): void
    {
        $this->redirect(ProductResource::getUrl('index'));
    }
}
