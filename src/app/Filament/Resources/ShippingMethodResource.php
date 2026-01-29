<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShippingMethodResource\Pages;
use App\Models\ShippingMethod;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ShippingMethodResource extends Resource
{
    protected static ?string $model = ShippingMethod::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationGroup = 'E-Ticaret';

    protected static ?string $modelLabel = 'Kargo Yöntemi';

    protected static ?string $pluralModelLabel = 'Kargo Yöntemleri';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Kargo';

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Kargo Bilgileri')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Ad')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Standart Kargo'),
                        Forms\Components\Textarea::make('description')
                            ->label('Açıklama')
                            ->rows(2)
                            ->maxLength(500)
                            ->placeholder('2-4 iş günü içinde teslimat'),
                        Forms\Components\TextInput::make('price')
                            ->label('Kargo Ücreti (₺)')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->required()
                            ->helperText('TL cinsinden sabit kargo ücreti'),
                        Forms\Components\TextInput::make('free_shipping_min_amount')
                            ->label('Ücretsiz Kargo Min. Tutar (₺)')
                            ->numeric()
                            ->minValue(0)
                            ->nullable()
                            ->helperText('Bu tutar ve üzeri siparişlerde kargo ücretsiz. Boş bırakılırsa her zaman ücret alınır.'),
                        Forms\Components\TextInput::make('estimated_days')
                            ->label('Tahmini Teslimat')
                            ->maxLength(100)
                            ->placeholder('2-4 iş günü'),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Sıra')
                            ->numeric()
                            ->default(0),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true)
                            ->helperText('Aktif olanlar ödeme sayfasında listelenir.'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Ad')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Ücret (₺)')
                    ->formatStateUsing(fn ($state) => number_format((float) $state, 2, ',', '.') . ' ₺')
                    ->sortable(),
                Tables\Columns\TextColumn::make('free_shipping_min_amount')
                    ->label('Ücretsiz min. (₺)')
                    ->formatStateUsing(fn ($state) => $state !== null ? number_format((float) $state, 2, ',', '.') . ' ₺' : '—')
                    ->sortable(),
                Tables\Columns\TextColumn::make('estimated_days')
                    ->label('Teslimat')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Sıra')
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
            'index' => Pages\ListShippingMethods::route('/'),
            'create' => Pages\CreateShippingMethod::route('/create'),
            'edit' => Pages\EditShippingMethod::route('/{record}/edit'),
        ];
    }
}
