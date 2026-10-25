<?php

namespace App\Filament\Resources\ProductVariationResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Filament\Resources\ProductVariationResource;
use App\Models\ProductVariation;
use App\Support\ProductVariationAdminSync;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

/**
 * @property ProductVariation $record
 */
class EditProductVariation extends EditRecord
{
    protected static string $resource = ProductVariationResource::class;

    protected ?string $maxContentWidth = 'full';

    /** Kayıttan önceki tip (tip değişince seçenekler kütüphaneden yenilenir). */
    protected ?string $typeBeforeSave = null;

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }

    public function getContentTabLabel(): ?string
    {
        return 'Varyasyon ayarları';
    }

    public function getContentTabIcon(): ?string
    {
        return 'heroicon-o-cog-6-tooth';
    }

    public function getTitle(): string|Htmlable
    {
        return 'Varyasyon: '.$this->record->name;
    }

    public function getSubheading(): ?string
    {
        return 'Ürün: '.($this->record->product?->name ?? '—');
    }

    public function getBreadcrumbs(): array
    {
        $product = $this->record->product;

        return array_filter([
            ProductResource::getUrl('index') => 'Ürünler',
            $product ? $this->productVariationsUrl() : null => $product?->name,
            '#' => $this->record->name,
        ]);
    }

    protected function productVariationsUrl(): string
    {
        return ProductResource::getUrl('edit', [
            'record' => $this->record->product_id,
            'activeRelationManager' => 0,
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('backToProduct')
                ->label('Ürünün varyasyonlarına dön')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(fn (): string => $this->productVariationsUrl()),
            Actions\DeleteAction::make()
                ->successRedirectUrl(fn (): string => $this->productVariationsUrl())
                ->after(function (): void {
                    if ($this->record->product) {
                        ProductVariationAdminSync::afterVariationsChanged($this->record->product);
                    }
                }),
        ];
    }

    protected function beforeSave(): void
    {
        $this->typeBeforeSave = (string) $this->record->getOriginal('type');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (ProductResource::variationTypeRequiresSingleSelectOnly((string) ($data['type'] ?? ''))) {
            $data['allows_multiple'] = false;
        }
        unset($data['options']);

        return $data;
    }

    protected function afterSave(): void
    {
        if ($this->typeBeforeSave !== null && $this->typeBeforeSave !== (string) $this->record->type) {
            ProductVariationAdminSync::replaceOptionsWithPresets($this->record);
        }

        ProductVariationAdminSync::afterVariationSaved($this->record);
    }
}
