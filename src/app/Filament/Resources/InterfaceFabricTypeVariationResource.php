<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InterfaceFabricTypeVariationResource\Pages;
use App\Models\InterfaceFabricTypeVariation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * Ana menü bağlantısı {@see \App\Providers\Filament\AdminPanelProvider} içinde özelleştirilir.
 *
 * Kayıtlar mağaza tarafında bağlamak için: {@see InterfaceFabricTypeVariation::forDisplay()}.
 */
class InterfaceFabricTypeVariationResource extends Resource
{
    protected static ?string $model = InterfaceFabricTypeVariation::class;

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';

    protected static ?string $modelLabel = 'Kumaş türü varyasyonu';

    protected static ?string $pluralModelLabel = 'Kumaş türü varyasyonları';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Kumaş türü varyasyonu')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Kumaş / tür adı (opsiyonel)')
                            ->maxLength(255),
                        Forms\Components\FileUpload::make('image_path')
                            ->label('Kumaş görseli')
                            ->directory('interface_fabric_type_variations')
                            ->visibility('public')
                            ->image()
                            ->imageEditor()
                            ->required()
                            ->helperText('Örn. doku / kumaş örneği swatch görseli.'),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Sıra')
                            ->numeric()
                            ->default(0)
                            ->helperText('Listede önce gelmesi için küçük sayı. Sürükleyerek de sıralayabilirsiniz.'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Yayında')
                            ->default(true),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('Görsel')
                    ->disk('public')
                    ->visibility('public')
                    ->height(50),
                Tables\Columns\TextColumn::make('name')
                    ->label('Ad'),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Sıra')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Yayında')
                    ->boolean(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Güncellendi')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Yayında'),
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
            'index' => Pages\ListInterfaceFabricTypeVariations::route('/'),
            'create' => Pages\CreateInterfaceFabricTypeVariation::route('/create'),
            'edit' => Pages\EditInterfaceFabricTypeVariation::route('/{record}/edit'),
        ];
    }
}
