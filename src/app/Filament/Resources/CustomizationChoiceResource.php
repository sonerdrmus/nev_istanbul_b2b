<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomizationChoiceResource\Pages;
use App\Models\CustomizationChoice;
use App\Models\Product;
use App\Models\ProductVariation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CustomizationChoiceResource extends Resource
{
    protected static ?string $model = CustomizationChoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-plus';

    protected static ?string $navigationGroup = 'Varyasyon yönetimi';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $modelLabel = 'özelleştirme seçeneği';

    protected static ?string $pluralModelLabel = 'Özelleştirme Seçeneği';

    protected static ?string $navigationLabel = 'Özelleştirme Seçeneği';

    protected static ?string $slug = 'customization-choices';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Varyasyon')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Varyasyon başlığı')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label('Varyasyon açıklaması')
                            ->rows(3)
                            ->maxLength(2000),
                        Forms\Components\Toggle::make('allows_multiple')
                            ->label('Çoklu seçenek seçilebilsin')
                            ->default(false)
                            ->helperText('Açıksa müşteri birden fazla seçenek işaretler. Kapalıysa tek seçenek seçer.'),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Sıra')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ])
                    ->columns(1),
                Forms\Components\Section::make('Seçenekler')
                    ->description('Müşterinin seçeceği seçenekler. Her seçeneğe görsel yüklenebilir.')
                    ->schema([
                        Forms\Components\Repeater::make('options')
                            ->relationship()
                            ->label('Seçenekler')
                            ->schema([
                                Forms\Components\TextInput::make('label')
                                    ->label('Seçenek')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                Forms\Components\FileUpload::make('image_path')
                                    ->label('Görsel')
                                    ->directory('customization_choice_options')
                                    ->visibility('public')
                                    ->image()
                                    ->imageEditor()
                                    ->nullable(),
                                Forms\Components\Select::make('image_size')
                                    ->label('Görsel boyutu')
                                    ->options([
                                        'small' => 'Küçük',
                                        'medium' => 'Orta',
                                        'large' => 'Büyük',
                                    ])
                                    ->default('medium')
                                    ->required()
                                    ->native(false)
                                    ->helperText('Ürün sayfasındaki varyasyon seçiminde görsel bu boyutta görünür.'),
                            ])
                            ->columns(2)
                            ->orderColumn('sort_order')
                            ->minItems(1)
                            ->defaultItems(1)
                            ->addActionLabel('Seçenek ekle')
                            ->columnSpanFull(),
                        Forms\Components\Placeholder::make('fixed_none_option')
                            ->label('Sabit seçenek')
                            ->content(CustomizationChoice::NONE_LABEL.' — bu seçenek her zaman listenin sonundadır ve silinemez. Çoklu seçimde işaretlenirse diğer seçenekler kapanır.'),
                    ]),
                Forms\Components\Section::make('Ürün ve sıra')
                    ->description('Bu özelleştirme hangi ürünlerde, hangi varyasyon seçiminden sonra sorulsun.')
                    ->schema([
                        Forms\Components\Repeater::make('assignments')
                            ->relationship()
                            ->label('Ürünler')
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->label('Ürün')
                                    ->searchable()
                                    ->required()
                                    ->distinct()
                                    ->live()
                                    ->options(fn (): array => Product::query()->orderBy('name')->pluck('name', 'id')->all())
                                    ->afterStateUpdated(fn (Set $set) => $set('after_product_variation_id', null)),
                                Forms\Components\Select::make('after_product_variation_id')
                                    ->label('Hangi varyasyon seçiminden sonra')
                                    ->searchable()
                                    ->required()
                                    ->options(function (Get $get): array {
                                        $productId = (int) $get('product_id');
                                        if ($productId <= 0) {
                                            return [];
                                        }

                                        return ProductVariation::query()
                                            ->where('product_id', $productId)
                                            ->orderBy('sort_order')
                                            ->orderBy('id')
                                            ->pluck('name', 'id')
                                            ->all();
                                    })
                                    ->rule(function (Get $get) {
                                        return function (string $attribute, mixed $value, \Closure $fail) use ($get): void {
                                            $variation = ProductVariation::query()->find($value);
                                            if (! $variation || (int) $variation->product_id !== (int) $get('product_id')) {
                                                $fail('Seçilen varyasyon bu ürüne ait değil.');
                                            }
                                        };
                                    }),
                            ])
                            ->columns(2)
                            ->minItems(1)
                            ->addActionLabel('Ürün ekle')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Başlık')
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Açıklama')
                    ->limit(80)
                    ->wrap()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('allows_multiple')
                    ->label('Çoklu')
                    ->boolean(),
                Tables\Columns\TextColumn::make('options_count')
                    ->label('Seçenek')
                    ->counts('options'),
                Tables\Columns\TextColumn::make('assignments_count')
                    ->label('Ürün')
                    ->counts('assignments'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->defaultSort('sort_order')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withCount(['options', 'assignments']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomizationChoices::route('/'),
            'create' => Pages\CreateCustomizationChoice::route('/create'),
            'edit' => Pages\EditCustomizationChoice::route('/{record}/edit'),
        ];
    }
}
