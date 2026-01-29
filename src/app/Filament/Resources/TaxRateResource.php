<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaxRateResource\Pages;
use App\Models\TaxRate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TaxRateResource extends Resource
{
    protected static ?string $model = TaxRate::class;

    protected static ?string $navigationIcon = 'heroicon-o-percent-badge';

    protected static ?string $navigationGroup = 'E-Ticaret';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $modelLabel = 'Vergi Oranı';

    protected static ?string $pluralModelLabel = 'Vergi Oranları';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Vergi Oranları';

    public static function getNavigationSort(): ?int
    {
        return 6;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Vergi Oranı')
                    ->schema([
                        Forms\Components\Select::make('tax_class_id')
                            ->label('Vergi Sınıfı')
                            ->relationship('taxClass', 'title', fn ($query) => $query->orderBy('sort_order')->orderBy('title'))
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('name')
                            ->label('Vergi Adı')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('KDV %18'),
                        Forms\Components\TextInput::make('rate')
                            ->label('Oran / Tutar')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->helperText('Yüzde için örn: 18. Yüzde için negatif değer (indirim) girebilirsiniz.'),
                        Forms\Components\Select::make('type')
                            ->label('Tip')
                            ->options([
                                TaxRate::TYPE_PERCENTAGE => 'Yüzde',
                                TaxRate::TYPE_FIXED => 'Sabit Tutar',
                            ])
                            ->default(TaxRate::TYPE_PERCENTAGE)
                            ->required()
                            ->live(),
                        Forms\Components\TextInput::make('geo_zone')
                            ->label('Bölge / Vergi Alanı')
                            ->maxLength(255)
                            ->placeholder('Türkiye, UK VAT Zone vb.')
                            ->nullable(),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Sıra')
                            ->numeric()
                            ->default(0),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Vergi Adı')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rate')
                    ->label('Oran')
                    ->formatStateUsing(fn ($state, TaxRate $record) => $record->type === TaxRate::TYPE_PERCENTAGE
                        ? number_format((float) $state, 2, ',', '.') . '%'
                        : number_format((float) $state, 2, ',', '.') . ' ₺')
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tip')
                    ->formatStateUsing(fn (string $state): string => $state === TaxRate::TYPE_PERCENTAGE ? 'Yüzde' : 'Sabit Tutar')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('taxClass.title')
                    ->label('Vergi Sınıfı')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('geo_zone')
                    ->label('Bölge')
                    ->placeholder('—')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Sıra')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->defaultSort('sort_order')
            ->filters([
                Tables\Filters\SelectFilter::make('tax_class_id')
                    ->label('Vergi Sınıfı')
                    ->relationship('taxClass', 'title')
                    ->searchable()
                    ->preload(),
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
            'index' => Pages\ListTaxRates::route('/'),
            'create' => Pages\CreateTaxRate::route('/create'),
            'edit' => Pages\EditTaxRate::route('/{record}/edit'),
        ];
    }
}
