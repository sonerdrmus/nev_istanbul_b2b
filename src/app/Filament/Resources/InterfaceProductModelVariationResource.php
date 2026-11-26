<?php

namespace App\Filament\Resources;

use App\Filament\Forms\Components\ProductMultiSelect;
use App\Filament\Forms\LocaleNameInputs;
use App\Filament\Resources\InterfaceProductModelVariationResource\Pages;
use App\Models\InterfaceProductModelVariation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Ana menü bağlantısı {@see \App\Providers\Filament\AdminPanelProvider} içinde özelleştirilir.
 */
class InterfaceProductModelVariationResource extends Resource
{
    protected static ?string $model = InterfaceProductModelVariation::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationGroup = 'Varyasyon yönetimi';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $modelLabel = 'Model';

    protected static ?string $pluralModelLabel = 'Modeller';

    protected static ?string $navigationLabel = 'Model Yönetimi';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Model')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Model adı (TR)')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Eşleştirme anahtarı. Çeviri için EN/IT kullanın.'),
                        ...LocaleNameInputs::make(),
                        Forms\Components\FileUpload::make('image_path')
                            ->label('Görsel')
                            ->directory('interface_product_model_variations')
                            ->visibility('public')
                            ->image()
                            ->imageEditor()
                            ->nullable()
                            ->helperText('Opsiyonel. Model önizleme görseli; mağazada model seçeneğinde ve ürün galerisinde gösterilir.'),
                        Forms\Components\FileUpload::make('size_table_image_path')
                            ->label('Beden Tablosu')
                            ->directory('interface_product_model_variations/size_tables')
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
                        ProductMultiSelect::relationship('products')
                            ->helperText('Bu model yalnızca seçili ürünlerin Model varyasyonunda seçenek olarak görünür. En az bir ürün seçilmelidir; boş bırakılırsa hiçbir üründe görünmez.'),
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withCount(['products']);
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
                    ->getStateUsing(fn (InterfaceProductModelVariation $record): bool => filled($record->size_table_image_path))
                    ->trueIcon('heroicon-o-table-cells')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('success')
                    ->falseColor('gray'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Model adı')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('products_count')
                    ->label('Ürün')
                    ->counts('products')
                    ->formatStateUsing(fn ($state): string => (int) $state === 0 ? 'Atanmamış' : (string) $state)
                    ->badge()
                    ->color(fn ($state): string => (int) $state === 0 ? 'warning' : 'success')
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
            'index' => Pages\ListInterfaceProductModelVariations::route('/'),
            'create' => Pages\CreateInterfaceProductModelVariation::route('/create'),
            'edit' => Pages\EditInterfaceProductModelVariation::route('/{record}/edit'),
        ];
    }
}
