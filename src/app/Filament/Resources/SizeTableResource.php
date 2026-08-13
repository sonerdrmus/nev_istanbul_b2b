<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SizeTableResource\Pages;
use App\Models\ProductVariation;
use App\Models\SizeTable;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SizeTableResource extends Resource
{
    protected static ?string $model = SizeTable::class;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';

    protected static ?string $navigationGroup = 'Varyasyon yönetimi';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $modelLabel = 'Sipariş Adeti';

    protected static ?string $pluralModelLabel = 'Beden tabloları';

    protected static ?string $navigationLabel = 'Beden tabloları';

    protected static ?string $recordTitleAttribute = 'name';

    /**
     * Varyasyon adları + alt seçenekler (Varyasyon → Seçenek) için seçenek listesi.
     * Değer: "VaryasyonAdı" veya "VaryasyonAdı|SeçenekDeğeri"
     *
     * @return array<string, string>
     */
    public static function getTriggerVariationOptions(): array
    {
        $variations = ProductVariation::query()->with('options')->orderBy('name')->get();
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
            $options[$varName . ' (herhangi)'] = $varName;
            foreach (array_keys($optValues) as $optVal) {
                $options[$varName . ' → ' . $optVal] = $varName . '|' . $optVal;
            }
        }
        return $options;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Tablo bilgisi')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Ad')
                            ->required()
                            ->maxLength(255)
                            ->disabled(fn (?SizeTable $record) => $record !== null),
                        Forms\Components\TextInput::make('title')
                            ->label('Başlık (ürün sayfasında görünen)')
                            ->placeholder('Örn: BEDEN TABLOSU (ERKEK)')
                            ->maxLength(255),
                        Forms\Components\Select::make('trigger_combined')
                            ->label('Hangi varyasyon seçildiğinde bu tablo açılsın?')
                            ->options(fn (): array => self::getTriggerVariationOptions())
                            ->searchable()
                            ->preload()
                            ->getOptionLabelUsing(function (?string $value): ?string {
                                if ($value === null || $value === '') {
                                    return null;
                                }
                                if (str_contains($value, '|')) {
                                    $parts = explode('|', $value, 2);
                                    return $parts[0] . ' → ' . $parts[1];
                                }
                                return $value . ' (herhangi)';
                            })
                            ->placeholder('Varyasyon veya seçenek seçin')
                            ->helperText('Varyasyon adı veya "Varyasyon → Seçenek" ile sadece o seçenekte tabloyu açabilirsiniz. Liste boşsa önce ürünlere varyasyon ekleyin.')
                            ->default(fn (?SizeTable $record): ?string => $record
                                ? ($record->trigger_variation_name . ($record->trigger_option_value ? '|' . $record->trigger_option_value : ''))
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
                Tables\Columns\TextColumn::make('trigger_variation_name')
                    ->label('Varyasyon')
                    ->formatStateUsing(fn (?string $state, SizeTable $record): string => $record->trigger_option_value
                        ? $state . ' → ' . $record->trigger_option_value
                        : ($state ?? '—'))
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('columns_count')
                    ->label('Kolon sayısı')
                    ->counts('columns'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([])
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSizeTables::route('/'),
            'edit' => Pages\EditSizeTable::route('/{record}/edit'),
        ];
    }

    public static function getNavigationSort(): ?int
    {
        return 15;
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
