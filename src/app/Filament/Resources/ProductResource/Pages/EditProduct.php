<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Support\ProductDimensionMultiplierSync;
use App\Support\ProductVariationOptionInterfaceSync;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected ?string $maxContentWidth = 'full';

    /** Ürün formu ve «Varyasyonlar» üst düzey sekmeler olarak gösterilir; yalnızca aktif sekme render edilir. */
    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }

    public function getContentTabLabel(): ?string
    {
        return 'Ürün bilgileri';
    }

    public function getContentTabIcon(): ?string
    {
        return 'heroicon-o-document-text';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Kumaş / kalıp seçenekleri her açılışta güncel: yeni kayıtlar eklenir, başka ürüne özel olanlar düşer.
        ProductVariationOptionInterfaceSync::reconcileFabricProductOptions((int) $this->record->getKey());
        ProductVariationOptionInterfaceSync::reconcileMoldModelProductOptions((int) $this->record->getKey());
        ProductVariationOptionInterfaceSync::reconcileSizeTableProductOptions((int) $this->record->getKey());
        ProductVariationOptionInterfaceSync::reconcilePackagingProductOptions((int) $this->record->getKey());

        // Varyasyonlar artık ayrı «Varyasyonlar» sekmesinde (VariationsRelationManager) yüklenir;
        // ürün formu seçenekleri yüklemez/render etmez.
        $this->record->load(['customizationRows']);

        $data['_product_id'] = $this->record->getKey();
        $data['dimension_multipliers'] = ProductDimensionMultiplierSync::loadTemplateForNewProduct();

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['_product_id']);

        // Seçili mevcut görsel yerine yeni yüklenen varsa DB alanına onu yaz.
        if (! empty($data['image_upload'])) {
            $upload = $data['image_upload'];
            $path = is_array($upload) ? ($upload[0] ?? null) : $upload;
            if (is_string($path) && trim($path) !== '') {
                $data['image'] = $path;
            }
        }
        unset($data['image_upload']);

        if (! empty($data['home_showcase_image_upload'])) {
            $upload = $data['home_showcase_image_upload'];
            $path = is_array($upload) ? ($upload[0] ?? null) : $upload;
            if (is_string($path) && trim($path) !== '') {
                $data['home_showcase_image'] = $path;
            }
        }
        unset($data['home_showcase_image_upload']);

        // Ürün tablosunda kolon değil; afterSave'de persist edilir.
        unset($data['dimension_multipliers']);

        // Varyasyonlar formda değil (Varyasyonlar sekmesinde ayrı kaydedilir).
        unset($data['variations']);

        return $data;
    }

    protected function afterSave(): void
    {
        ProductResource::syncPackagingAssignmentsFromProduct($this->record);
        ProductVariationOptionInterfaceSync::reconcilePackagingProductOptions((int) $this->record->getKey());
    }
}
