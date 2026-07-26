<?php

namespace App\Filament\Resources;

use App\Filament\Forms\Components\ProductMultiSelect;
use App\Filament\Resources\InterfaceFabricTypeVariationResource\Pages;
use App\Models\InterfaceColorVariation;
use App\Models\InterfaceFabricTypeVariation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
                        Forms\Components\Section::make('Kumaş görseli')
                            ->description('Görsel opsiyoneldir. Altına detay metni ekleyebilirsiniz; mağazada «detaylı bilgi» modalında gösterilir.')
                            ->schema([
                                Forms\Components\FileUpload::make('image_path')
                                    ->label('Görsel')
                                    ->directory('interface_fabric_type_variations')
                                    ->visibility('public')
                                    ->image()
                                    ->imageEditor()
                                    ->nullable()
                                    ->helperText('Opsiyonel. Örn. doku / kumaş örneği swatch görseli.'),
                                Forms\Components\Textarea::make('detail_text')
                                    ->label('Detaylı bilgi metni')
                                    ->rows(4)
                                    ->maxLength(5000)
                                    ->nullable()
                                    ->helperText('Opsiyonel. Mağazada kumaş adının yanında info ikonu ve «detaylı bilgi» ile modalda açılır.'),
                            ])
                            ->columns(1),
                        Forms\Components\TextInput::make('price_multiplier')
                            ->label('Fiyat çarpanı (×)')
                            ->numeric()
                            ->default(1)
                            ->minValue(0)
                            ->step(0.01)
                            ->required()
                            ->helperText('1 = temel fiyat aynı kalır; 1,50 girildiğinde mağazada birim fiyat × 1,50 olur.'),
                        Forms\Components\Select::make('colorVariations')
                            ->label('Renkler')
                            ->relationship(
                                name: 'colorVariations',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query) => $query->orderBy('sort_order')->orderBy('id'),
                            )
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->getOptionLabelFromRecordUsing(function (InterfaceColorVariation $record): string {
                                $name = trim((string) ($record->name ?? ''));

                                return $name !== '' ? $name : ('Renk #'.$record->getKey());
                            })
                            ->helperText('Renk Varyasyonları sayfasındaki kayıtlardan seçin. Mağazada bu kumaş seçildiğinde yalnızca seçili renkler listelenir.'),
                        ProductMultiSelect::relationship('products')
                            ->helperText('Bu kumaş yalnızca seçili ürünlerin Kumaş Türü varyasyonunda seçenek olarak görünür. En az bir ürün seçilmelidir; boş bırakılırsa hiçbir üründe görünmez.'),
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
        return parent::getEloquentQuery()->withCount(['colorVariations', 'products']);
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
                Tables\Columns\TextColumn::make('colorVariations_count')
                    ->label('Renk')
                    ->counts('colorVariations')
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
                    ->formatStateUsing(fn ($state): string => '×'.number_format((float) $state, 2, ',', '.'))
                    ->sortable(),
                Tables\Columns\IconColumn::make('detail_text')
                    ->label('Detay')
                    ->boolean()
                    ->getStateUsing(fn (InterfaceFabricTypeVariation $record): bool => filled($record->detail_text))
                    ->trueIcon('heroicon-o-information-circle')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('info'),
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
