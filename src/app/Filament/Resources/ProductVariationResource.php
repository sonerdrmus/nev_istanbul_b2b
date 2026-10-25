<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductVariationResource\Pages;
use App\Filament\Resources\ProductVariationResource\RelationManagers;
use App\Models\ProductVariation;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;

/**
 * Tek bir ürün varyasyonunun düzenleme sayfası (menüde görünmez).
 * Ürün → Varyasyonlar sekmesinden açılır; seçenekler ayrı «Seçenekler» sekmesinde tek tek düzenlenir.
 */
class ProductVariationResource extends Resource
{
    protected static ?string $model = ProductVariation::class;

    protected static ?string $slug = 'product-variations';

    protected static ?string $modelLabel = 'Varyasyon';

    protected static ?string $pluralModelLabel = 'Varyasyonlar';

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $shouldRegisterNavigation = false;

    protected static bool $isGloballySearchable = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema(ProductResource::variationFormSchema())
            ->columns(2);
    }

    public static function canCreate(): bool
    {
        // Varyasyon, ürün sayfasındaki «Varyasyonlar» sekmesinden eklenir.
        return false;
    }

    public static function canViewAny(): bool
    {
        return true;
    }

    public static function canEdit(Model $record): bool
    {
        return true;
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\OptionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductVariations::route('/'),
            'edit' => Pages\EditProductVariation::route('/{record}/edit'),
        ];
    }
}
