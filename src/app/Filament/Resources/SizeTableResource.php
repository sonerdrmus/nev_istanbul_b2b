<?php

namespace App\Filament\Resources;

use App\Filament\Forms\Components\ProductMultiSelect;
use App\Filament\Forms\LocaleNameInputs;
use App\Filament\Resources\SizeTableResource\Pages;
use App\Models\ProductVariation;
use App\Models\SizeTable;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SizeTableResource extends Resource
{
    protected static ?string $model = SizeTable::class;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';

    protected static ?string $navigationGroup = 'Varyasyon yönetimi';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $modelLabel = 'Beden tablosu';

    protected static ?string $pluralModelLabel = 'Beden tabloları';

    protected static ?string $navigationLabel = 'Beden tabloları';

    protected static ?string $recordTitleAttribute = 'name';

    /**
     * Varyasyon adları + alt seçenekler (Varyasyon → Seçenek) için seçenek listesi.
     * Değer: "VaryasyonAdı" veya "VaryasyonAdı|SeçenekDeğeri"
     *
     * @param  array<int, int|string>|null  $productIds  Verilirse yalnızca bu ürünlerin varyasyonları listelenir.
     * @return array<string, string>
     */
    public static function getTriggerVariationOptions(?array $productIds = null): array
    {
        $productIds = $productIds === null
            ? null
            : array_values(array_unique(array_filter(array_map(
                static fn ($id): int => (int) $id,
                $productIds,
            ), static fn (int $id): bool => $id > 0)));

        $variations = ProductVariation::query()
            ->with('options')
            ->when(
                $productIds !== null && $productIds !== [],
                fn (Builder $q) => $q->whereIn('product_id', $productIds),
            )
            ->orderBy('name')
            ->get();

        $grouped = [];
        foreach ($variations as $v) {
            $name = trim((string) $v->name);
            if ($name === '') {
                continue;
            }
            if (! isset($grouped[$name])) {
                $grouped[$name] = [];
            }
            foreach ($v->options as $opt) {
                $val = trim((string) $opt->option_value);
                if ($val !== '') {
                    $grouped[$name][$val] = true;
                }
            }
        }

        $options = [];
        foreach ($grouped as $varName => $optValues) {
            $options[$varName] = $varName.' (herhangi)';
            foreach (array_keys($optValues) as $optVal) {
                $options[$varName.'|'.$optVal] = $varName.' → '.$optVal;
            }
        }

        return $options;
    }

    /**
     * @param  mixed  $productsState
     * @return array<int, int>
     */
    public static function productIdsFromFormState(mixed $productsState): array
    {
        if (! is_array($productsState) || $productsState === []) {
            return [];
        }

        return array_values(array_unique(array_filter(array_map(
            static function ($value): int {
                if (is_numeric($value)) {
                    return (int) $value;
                }

                return 0;
            },
            $productsState,
        ), static fn (int $id): bool => $id > 0)));
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Tablo bilgisi')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Ad (TR)')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Kayıt kimliği. Eşleştirme bu addan yürür; çeviri için EN/IT kullanın.')
                            ->disabled(fn (?SizeTable $record) => $record !== null),
                        ...LocaleNameInputs::make(),
                        Forms\Components\TextInput::make('title')
                            ->label('Başlık (TR, ürün sayfasında görünen)')
                            ->placeholder('Örn: BEDEN TABLOSU (ERKEK)')
                            ->maxLength(255),
                        ...LocaleNameInputs::make('title', 'Başlık (EN)', 'Başlık (IT)'),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Sıra')
                            ->numeric()
                            ->default(0)
                            ->helperText('Listede önce gelmesi için küçük sayı.'),
                        ProductMultiSelect::relationship('products')
                            ->live()
                            ->helperText('Opsiyonel. Boş bırakılırsa beden tablosu varyasyonu olan tüm ürünlerde kullanılabilir. Ürün seçilirse yalnızca o ürünlerde seçenek olur ve aşağıdaki tetikleyici o ürünlerin varyasyonlarından listelenir.'),
                        Forms\Components\Select::make('trigger_combined')
                            ->label('Hangi varyasyon seçildiğinde bu tablo açılsın?')
                            ->options(function (Get $get): array {
                                $productIds = static::productIdsFromFormState($get('products'));

                                return static::getTriggerVariationOptions($productIds !== [] ? $productIds : null);
                            })
                            ->searchable()
                            ->preload()
                            ->getOptionLabelUsing(function (?string $value): ?string {
                                if ($value === null || $value === '') {
                                    return null;
                                }
                                if (str_contains($value, '|')) {
                                    $parts = explode('|', $value, 2);

                                    return $parts[0].' → '.$parts[1];
                                }

                                return $value.' (herhangi)';
                            })
                            ->placeholder('Varyasyon veya seçenek seçin')
                            ->helperText(function (Get $get): string {
                                $productIds = static::productIdsFromFormState($get('products'));
                                if ($productIds !== []) {
                                    return 'Seçili ürünlerin varyasyonlarından seçin. «Varyasyon → Seçenek» ile yalnızca o seçenekte tablo açılır.';
                                }

                                return 'Ürün seçilmediyse tüm ürünlerin varyasyonları listelenir. İsterseniz önce ürün seçerek listeyi daraltın.';
                            })
                            ->default(fn (?SizeTable $record): ?string => $record
                                ? ($record->trigger_variation_name.($record->trigger_option_value ? '|'.$record->trigger_option_value : ''))
                                : null)
                            ->dehydrated(false),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Beden kolonları')
                    ->description('Tabloda gösterilecek beden kolonları (XS, S, M veya 98, 104 vb.). Sırayı değiştirmek için sürükleyin. Çarpan miktarı: birim ürün fiyatı ile çarpılır; sipariş satır tutarı Σ(adet × birim fiyat × çarpan) olarak hesaplanır (varsayılan 1).')
                    ->schema([
                        Forms\Components\Repeater::make('columns')
                            ->relationship()
                            ->reorderable()
                            ->reorderableWithButtons()
                            ->itemLabel(fn (array $state): ?string => $state['size_value'] ?? 'Beden')
                            ->addActionLabel('Kolon ekle')
                            ->schema([
                                Forms\Components\TextInput::make('size_value')
                                    ->label('Beden değeri')
                                    ->placeholder('XS, S, 98, 104...')
                                    ->required()
                                    ->maxLength(50),
                                Forms\Components\TextInput::make('sort_order')
                                    ->label('Sıra')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0),
                                Forms\Components\TextInput::make('price_multiplier')
                                    ->label('Çarpan miktarı')
                                    ->helperText('Birim fiyat ile çarpılır. Örn: 1,009 — o beden satırı adet × (fiyat × 1,009). 3 ondalığa kadar.')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(0)
                                    ->step(0.001)
                                    ->required(),
                            ])
                            ->columns(3)
                            ->defaultItems(0),
                    ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withCount(['columns', 'products']);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Tablo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Başlık')
                    ->limit(40)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('products_count')
                    ->label('Ürün')
                    ->counts('products')
                    ->formatStateUsing(fn ($state): string => (int) $state === 0 ? 'Tümü' : (string) $state)
                    ->badge()
                    ->color(fn ($state): string => (int) $state === 0 ? 'gray' : 'success')
                    ->sortable(),
                Tables\Columns\TextColumn::make('trigger_variation_name')
                    ->label('Varyasyon')
                    ->formatStateUsing(fn (?string $state, SizeTable $record): string => $record->trigger_option_value
                        ? $state.' → '.$record->trigger_option_value
                        : ($state ?? '—'))
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('columns_count')
                    ->label('Kolon sayısı')
                    ->counts('columns'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSizeTables::route('/'),
            'create' => Pages\CreateSizeTable::route('/create'),
            'edit' => Pages\EditSizeTable::route('/{record}/edit'),
        ];
    }

    public static function getNavigationSort(): ?int
    {
        return 15;
    }

    /**
     * Form state'teki trigger_combined değerini DB alanlarına böler.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function applyTriggerCombinedToFormData(array $data, mixed $combined): array
    {
        if (is_string($combined) && $combined !== '') {
            if (str_contains($combined, '|')) {
                $parts = explode('|', $combined, 2);
                $data['trigger_variation_name'] = trim($parts[0]);
                $data['trigger_option_value'] = trim($parts[1]) ?: null;
            } else {
                $data['trigger_variation_name'] = trim($combined);
                $data['trigger_option_value'] = null;
            }
        } else {
            $data['trigger_variation_name'] = null;
            $data['trigger_option_value'] = null;
        }

        unset($data['trigger_combined']);

        return $data;
    }
}
