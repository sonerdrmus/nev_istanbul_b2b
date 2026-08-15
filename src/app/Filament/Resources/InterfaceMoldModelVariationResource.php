<?php

namespace App\Filament\Resources;

use App\Filament\Forms\LocaleNameInputs;
use App\Filament\Resources\InterfaceMoldModelVariationResource\Pages;
use App\Models\InterfaceMoldModelVariation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * Ana menü bağlantısı {@see \App\Providers\Filament\AdminPanelProvider} içinde özelleştirilir.
 */
class InterfaceMoldModelVariationResource extends Resource
{
    protected static ?string $model = InterfaceMoldModelVariation::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationGroup = 'Varyasyon yönetimi';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $modelLabel = 'Kalıp modeli';

    protected static ?string $pluralModelLabel = 'Kalıp modelleri';

    protected static ?string $navigationLabel = 'Kalıp Modeli Yönetimi';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Kalıp modeli')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Kalıp model adı (TR)')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Eşleştirme anahtarı. Çeviri için EN/IT kullanın.'),
                        ...LocaleNameInputs::make(),
                        Forms\Components\FileUpload::make('image_path')
                            ->label('Görsel')
                            ->directory('interface_mold_model_variations')
                            ->visibility('public')
                            ->image()
                            ->imageEditor()
                            ->nullable()
                            ->helperText('Opsiyonel. Kalıp modeli önizleme görseli.'),
                        Forms\Components\FileUpload::make('size_table_image_path')
                            ->label('Beden Tablosu')
                            ->directory('interface_mold_model_variations/size_tables')
                            ->visibility('public')
                            ->image()
                            ->imageEditor()
                            ->nullable()
                            ->helperText('Opsiyonel. Mağazada seçeneğin yanında «Beden tablosunu görüntüle» ile modalda açılır.'),
                        Forms\Components\TextInput::make('price_multiplier')
                            ->label('Fiyat çarpanı (×)')
                            ->numeric()
                            ->default(1)
                            ->minValue(0)
                            ->step(0.001)
                            ->required()
                            ->helperText('1 = temel fiyat aynı kalır; 1,009 girildiğinde birim fiyat × 1,009 olur (3 ondalığa kadar).'),
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
                Tables\Columns\IconColumn::make('size_table_image_path')
                    ->label('Beden tablosu')
                    ->boolean()
                    ->getStateUsing(fn (InterfaceMoldModelVariation $record): bool => filled($record->size_table_image_path))
                    ->trueIcon('heroicon-o-table-cells')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('success')
                    ->falseColor('gray'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Kalıp model adı')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price_multiplier')
                    ->label('Fiyat çarpanı')
                    ->formatStateUsing(fn ($state): string => '×'.number_format((float) $state, 3, ',', '.'))
                    ->sortable(),
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
            'index' => Pages\ListInterfaceMoldModelVariations::route('/'),
            'create' => Pages\CreateInterfaceMoldModelVariation::route('/create'),
            'edit' => Pages\EditInterfaceMoldModelVariation::route('/{record}/edit'),
        ];
    }
}
