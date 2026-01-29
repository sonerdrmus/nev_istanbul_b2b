<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaxClassResource\Pages;
use App\Models\TaxClass;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TaxClassResource extends Resource
{
    protected static ?string $model = TaxClass::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'E-Ticaret';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $modelLabel = 'Vergi Sınıfı';

    protected static ?string $pluralModelLabel = 'Vergi Sınıfları';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationLabel = 'Vergi Sınıfları';

    public static function getNavigationSort(): ?int
    {
        return 5;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Vergi Sınıfı')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Başlık')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Vergiye tabi ürünler'),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Sıra')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Vergi Sınıfı')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tax_rates_count')
                    ->label('Vergi Oranı')
                    ->counts('taxRates')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Sıra')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
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
            'index' => Pages\ListTaxClasses::route('/'),
            'create' => Pages\CreateTaxClass::route('/create'),
            'edit' => Pages\EditTaxClass::route('/{record}/edit'),
        ];
    }
}
