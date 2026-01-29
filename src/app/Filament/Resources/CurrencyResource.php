<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CurrencyResource\Pages;
use App\Models\Currency;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CurrencyResource extends Resource
{
    protected static ?string $model = Currency::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationGroup = 'E-Ticaret';

    protected static ?string $modelLabel = 'Para Birimi';

    protected static ?string $pluralModelLabel = 'Para Birimleri';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationSort(): ?int
    {
        return 4;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Para Birimi Bilgileri')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label('Kod')
                            ->required()
                            ->maxLength(10)
                            ->placeholder('TRY, USD, EUR')
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('name')
                            ->label('Ad')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Türk Lirası'),
                        Forms\Components\TextInput::make('symbol')
                            ->label('Sembol')
                            ->required()
                            ->maxLength(10)
                            ->placeholder('₺, $, €'),
                        Forms\Components\TextInput::make('exchange_rate')
                            ->label('Döviz Kuru (TL karşılığı)')
                            ->numeric()
                            ->default(1.0)
                            ->minValue(0.0001)
                            ->step(0.0001)
                            ->required()
                            ->helperText('TRY için 1.0, diğerleri için 1 birim = kaç TL (örn. USD: 34.50). TCMB kurları her saat otomatik güncellenir.'),
                        Forms\Components\TextInput::make('decimal_places')
                            ->label('Ondalık Basamak')
                            ->numeric()
                            ->default(2)
                            ->minValue(0)
                            ->maxValue(4),
                        Forms\Components\Toggle::make('is_default')
                            ->label('Varsayılan para birimi')
                            ->default(false)
                            ->helperText('Sadece bir para birimi varsayılan olabilir.'),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Sıra')
                            ->numeric()
                            ->default(0),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Kod')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Ad')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('symbol')
                    ->label('Sembol')
                    ->sortable(),
                Tables\Columns\TextColumn::make('exchange_rate')
                    ->label('Kur (TL)')
                    ->formatStateUsing(fn ($state, $record) => $record->code === 'TRY' ? '1.0000' : number_format((float) $state, 4, ',', '.'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('decimal_places')
                    ->label('Ondalık')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_default')
                    ->label('Varsayılan')
                    ->boolean(),
                Tables\Columns\TextColumn::make('products_count')
                    ->label('Ürün')
                    ->counts('products')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->defaultSort('sort_order')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Aktif'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCurrencies::route('/'),
            'create' => Pages\CreateCurrency::route('/create'),
            'edit' => Pages\EditCurrency::route('/{record}/edit'),
        ];
    }
}
