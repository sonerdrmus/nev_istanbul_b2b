<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InterfaceDeliveryMethodVariationResource\Pages;
use App\Models\InterfaceDeliveryMethodVariation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * Ana menü bağlantısı {@see \App\Providers\Filament\AdminPanelProvider} içinde özelleştirilir.
 */
class InterfaceDeliveryMethodVariationResource extends Resource
{
    protected static ?string $model = InterfaceDeliveryMethodVariation::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationGroup = 'Varyasyon yönetimi';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $modelLabel = 'Teslim şekli';

    protected static ?string $pluralModelLabel = 'Teslim şekilleri';

    protected static ?string $navigationLabel = 'Teslim Şeklini Yönet';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Teslim şekli')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Teslimat türü adı')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label('Açıklama')
                            ->rows(4)
                            ->maxLength(2000)
                            ->nullable()
                            ->helperText('Opsiyonel. Teslimat türü hakkında kısa açıklama.'),
                        Forms\Components\TextInput::make('estimated_delivery_time')
                            ->label('Tahmini teslimat süresi')
                            ->maxLength(255)
                            ->nullable()
                            ->placeholder('Örn. 15-20 iş günü, 3-5 hafta')
                            ->helperText('Mağazada teslim şekli seçildiğinde “Seçilen seçenekler” bölümünde belirgin şekilde gösterilir.'),
                        Forms\Components\FileUpload::make('image_path')
                            ->label('Görsel')
                            ->directory('interface_delivery_method_variations')
                            ->visibility('public')
                            ->image()
                            ->imageEditor()
                            ->nullable()
                            ->helperText('Opsiyonel. Teslim şekli önizleme görseli.'),
                        Forms\Components\TextInput::make('price_multiplier')
                            ->label('Fiyat çarpanı (×)')
                            ->numeric()
                            ->default(1)
                            ->minValue(0)
                            ->step(0.01)
                            ->required()
                            ->helperText('1 = temel fiyat aynı kalır; 1,50 girildiğinde birim fiyat × 1,50 olur.'),
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
                Forms\Components\Section::make('Alt teslim şekilleri')
                    ->description('Bu teslim şekli seçildiğinde mağaza ürün sayfasında alt seçenekler bilgi paneli olarak gösterilir. Boş bırakılırsa alt adım çıkmaz.')
                    ->schema([
                        Forms\Components\Repeater::make('subOptions')
                            ->relationship('subOptions')
                            ->label('')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Alt teslim şekli')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('description')
                                    ->label('Bilgi metni')
                                    ->rows(3)
                                    ->maxLength(2000)
                                    ->nullable()
                                    ->helperText('Seçildiğinde alt panelde gösterilir.'),
                                Forms\Components\TextInput::make('price_multiplier')
                                    ->label('Fiyat çarpanı (×)')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->required()
                                    ->helperText('Ana teslim şekli çarpanına ek olarak uygulanır.'),
                                Forms\Components\TextInput::make('sort_order')
                                    ->label('Sıra')
                                    ->numeric()
                                    ->default(0),
                                Forms\Components\Toggle::make('is_default')
                                    ->label('Varsayılan')
                                    ->default(false)
                                    ->inline(false),
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Aktif')
                                    ->default(true)
                                    ->inline(false),
                            ])
                            ->orderColumn('sort_order')
                            ->reorderableWithDragAndDrop()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                            ->addActionLabel('Alt teslim şekli ekle')
                            ->defaultItems(0),
                    ]),
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
                    ->label('Teslimat türü adı')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('estimated_delivery_time')
                    ->label('Tahmini süre')
                    ->limit(30)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Açıklama')
                    ->limit(40)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('sub_options_count')
                    ->label('Alt seçenek')
                    ->counts('subOptions')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price_multiplier')
                    ->label('Fiyat çarpanı')
                    ->formatStateUsing(fn ($state): string => '×'.number_format((float) $state, 2, ',', '.'))
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
            'index' => Pages\ListInterfaceDeliveryMethodVariations::route('/'),
            'create' => Pages\CreateInterfaceDeliveryMethodVariation::route('/create'),
            'edit' => Pages\EditInterfaceDeliveryMethodVariation::route('/{record}/edit'),
        ];
    }
}
