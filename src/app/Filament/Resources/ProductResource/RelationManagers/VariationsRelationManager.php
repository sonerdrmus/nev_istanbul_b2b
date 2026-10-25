<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use App\Filament\Resources\ProductResource;
use App\Filament\Resources\ProductVariationResource;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Support\ProductVariationAdminSync;
use App\Support\ProductVariationFlowSteps;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Ürün düzenleme sayfasındaki «Varyasyonlar» sekmesi.
 *
 * Eskiden tüm varyasyonlar ve seçenekleri ürün formunda iç içe repeater olarak render ediliyordu
 * (91 seçenekli bir üründe ~9 MB HTML, her etkileşimde ~3 sn). Artık:
 *  - Sekme ayrı ve lazy yüklenen bir Livewire bileşenidir; ürün formu bu sekmeyi render etmez.
 *  - Liste yalnızca varyasyon satırlarını gösterir (seçenekler yüklenmez).
 *  - Bir varyasyon kendi sayfasında (ProductVariationResource) düzenlenir; seçenekleri orada
 *    ayrı «Seçenekler» sekmesinde tablo olarak listelenir ve her seçenek tek başına düzenlenir.
 */
class VariationsRelationManager extends RelationManager
{
    protected static string $relationship = 'variations';

    protected static ?string $title = 'Varyasyonlar';

    protected static ?string $icon = 'heroicon-o-squares-2x2';

    protected static ?string $modelLabel = 'varyasyon';

    protected static ?string $pluralModelLabel = 'varyasyonlar';

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $isLazy = true;

    public static function getBadge(Model $ownerRecord, string $pageClass): ?string
    {
        return (string) $ownerRecord->variations()->count();
    }

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema(ProductResource::variationFormSchema())
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        $typeLabels = ProductResource::variationTypeLabels();

        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->withCount('options'))
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->paginated(false)
            ->emptyStateHeading('Henüz varyasyon yok')
            ->emptyStateDescription('Renk, beden, kumaş vb. varyasyonları «Varyasyon ekle» ile tanımlayın.')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Varyasyon')
                    ->weight('semibold')
                    ->description(fn (ProductVariation $record): ?string => collect([$record->name_en, $record->name_it])
                        ->filter(fn ($v) => filled($v))
                        ->implode(' · ') ?: null)
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tip')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => $typeLabels[(string) $state] ?? (string) $state),
                Tables\Columns\TextColumn::make('options_count')
                    ->label('Seçenek')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('depends_on')
                    ->label('Bağlı olduğu adım')
                    ->formatStateUsing(fn (?string $state): string => ProductVariationFlowSteps::isCustomizationDependency($state)
                        ? ProductVariationFlowSteps::customizationDependsOnLabel()
                        : (string) $state)
                    ->placeholder('Bağımsız'),
                Tables\Columns\IconColumn::make('allows_multiple')
                    ->label('Çoklu seçim')
                    ->boolean()
                    ->alignCenter(),
            ])
            ->recordUrl(fn (ProductVariation $record): string => ProductVariationResource::getUrl('edit', ['record' => $record]))
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Varyasyon ekle')
                    ->icon('heroicon-o-plus')
                    ->slideOver()
                    ->modalWidth(MaxWidth::FiveExtraLarge)
                    ->modalHeading('Yeni varyasyon')
                    ->modalDescription('Kaydettikten sonra seçenekleri eklemek için varyasyon sayfası açılır. Kütüphane tiplerinde (renk, kumaş, ambalaj vb.) seçenekler otomatik doldurulur.')
                    ->createAnother(false)
                    ->mutateFormDataUsing(fn (array $data): array => $this->prepareVariationData($data, true))
                    ->after(fn (ProductVariation $record) => ProductVariationAdminSync::afterVariationSaved($record))
                    ->successRedirectUrl(fn (ProductVariation $record): string => ProductVariationResource::getUrl('edit', [
                        'record' => $record,
                        'activeRelationManager' => 0,
                    ])),
            ])
            ->actions([
                Tables\Actions\Action::make('editOptions')
                    ->label('Düzenle')
                    ->icon('heroicon-o-pencil-square')
                    ->url(fn (ProductVariation $record): string => ProductVariationResource::getUrl('edit', ['record' => $record])),
                Tables\Actions\DeleteAction::make()
                    ->after(fn () => ProductVariationAdminSync::afterVariationsChanged($this->getOwnerRecord())),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->after(fn () => ProductVariationAdminSync::afterVariationsChanged($this->getOwnerRecord())),
                ]),
            ]);
    }

    /**
     * Eski ürün formu kaydındaki (finalizeVariationOptionsInProductFormData) varyasyon düzeyindeki kurallar.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function prepareVariationData(array $data, bool $creating): array
    {
        if (ProductResource::variationTypeRequiresSingleSelectOnly((string) ($data['type'] ?? ''))) {
            $data['allows_multiple'] = false;
        }
        // Tip seçilince form içine yazılan önizleme satırları; seçenekler kayıttan sonra oluşturulur.
        unset($data['options']);

        // Yeni varyasyon listenin sonuna eklensin (sıra elle girilmediyse).
        if ($creating && (int) ($data['sort_order'] ?? 0) === 0) {
            $max = (int) $this->getOwnerRecord()->variations()->max('sort_order');
            $data['sort_order'] = $max + 10;
        }

        return $data;
    }
}
