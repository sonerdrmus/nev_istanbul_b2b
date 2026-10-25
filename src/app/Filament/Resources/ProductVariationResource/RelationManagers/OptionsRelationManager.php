<?php

namespace App\Filament\Resources\ProductVariationResource\RelationManagers;

use App\Filament\Resources\ProductResource;
use App\Models\ProductVariation;
use App\Models\ProductVariationOption;
use App\Support\MediaUrl;
use App\Support\ProductVariationAdminSync;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

/**
 * Varyasyon sayfasındaki «Seçenekler» sekmesi: seçenekler tablo olarak listelenir,
 * her seçenek kendi penceresinde tek başına düzenlenir (yalnızca o seçeneğin formu render edilir).
 */
class OptionsRelationManager extends RelationManager
{
    protected static string $relationship = 'options';

    protected static ?string $title = 'Seçenekler';

    protected static ?string $icon = 'heroicon-o-list-bullet';

    protected static ?string $modelLabel = 'seçenek';

    protected static ?string $pluralModelLabel = 'seçenekler';

    protected static ?string $recordTitleAttribute = 'option_value';

    protected static bool $isLazy = true;

    public static function getBadge(Model $ownerRecord, string $pageClass): ?string
    {
        return (string) $ownerRecord->options()->count();
    }

    public function isReadOnly(): bool
    {
        return false;
    }

    protected function variation(): ProductVariation
    {
        /** @var ProductVariation */
        return $this->getOwnerRecord();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema(ProductResource::variationOptionFormSchema())
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        $type = (string) $this->variation()->type;
        $isPreset = ProductVariationAdminSync::isPresetType($type);

        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->paginated([25, 50, 100, 'all'])
            ->defaultPaginationPageOption(25)
            ->emptyStateHeading('Henüz seçenek yok')
            ->emptyStateDescription($isPreset
                ? 'Bu tipteki seçenekler Varyasyon yönetimi kütüphanesinden gelir. Varyasyonu kaydettiğinizde liste boşsa otomatik doldurulur.'
                : 'Seçenekleri «Seçenek ekle» ile tanımlayın.')
            ->columns([
                Tables\Columns\ImageColumn::make('option_image')
                    ->label('')
                    ->getStateUsing(fn (ProductVariationOption $record): ?string => filled($record->option_image) ? MediaUrl::public($record->option_image) : null)
                    ->square()
                    ->size(40)
                    ->visible($type !== 'size_table'),
                Tables\Columns\ColorColumn::make('option_color')
                    ->label('Renk')
                    ->visible($type === 'color'),
                Tables\Columns\TextColumn::make('option_value')
                    ->label('Seçenek')
                    ->weight('semibold')
                    ->description(fn (ProductVariationOption $record): ?string => collect([$record->option_value_en, $record->option_value_it])
                        ->filter(fn ($v) => filled($v))
                        ->implode(' · ') ?: null)
                    ->searchable(),
                Tables\Columns\TextColumn::make('price_delta')
                    ->label('Fiyat çarpanı')
                    ->numeric(3)
                    ->alignEnd(),
                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('Stok')
                    ->placeholder('—')
                    ->alignEnd()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('parent_option_ids')
                    ->label('Bağlı üst seçenek')
                    ->formatStateUsing(fn ($state): string => is_array($state) ? count($state).' seçenek' : (string) $state)
                    ->placeholder('Tümü')
                    ->visible(filled($this->variation()->depends_on)),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Seçenek ekle')
                    ->icon('heroicon-o-plus')
                    ->modalWidth(MaxWidth::FourExtraLarge)
                    ->createAnother(true)
                    ->visible(! $isPreset)
                    ->mutateFormDataUsing(function (array $data): array {
                        if ((int) ($data['sort_order'] ?? 0) === 0) {
                            $data['sort_order'] = (int) $this->variation()->options()->max('sort_order') + 10;
                        }

                        return $data;
                    })
                    ->after(fn () => $this->afterOptionsChanged()),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalWidth(MaxWidth::FourExtraLarge)
                    ->modalHeading(fn (ProductVariationOption $record): string => 'Seçeneği düzenle: '.$record->option_value)
                    ->after(fn () => $this->afterOptionsChanged()),
                Tables\Actions\DeleteAction::make()
                    ->after(fn () => $this->afterOptionsChanged()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->after(fn () => $this->afterOptionsChanged()),
                ]),
            ]);
    }

    protected function afterOptionsChanged(): void
    {
        // Silme sonrası liste boşalsa bile hemen kütüphaneden doldurma (kullanıcı bilerek silmiş olabilir).
        ProductVariationAdminSync::afterVariationSaved($this->variation()->fresh(), fillPresetsIfEmpty: false);
    }
}
