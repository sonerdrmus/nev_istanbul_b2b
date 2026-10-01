<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExtraVariationResource\Pages;
use App\Models\ExtraVariation;
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

class ExtraVariationResource extends Resource
{
    protected static ?string $model = ExtraVariation::class;

    protected static ?string $navigationIcon = 'heroicon-o-plus-circle';

    protected static ?string $navigationGroup = 'Varyasyon yönetimi';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $modelLabel = 'ekstra varyasyon';

    protected static ?string $pluralModelLabel = 'Ekstra Varyasyon Oluştur';

    protected static ?string $navigationLabel = 'Ekstra Varyasyon Oluştur';

    protected static ?string $slug = 'extra-variations';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Varyasyon')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Varyasyon başlığı')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Adım başlığı. Ürün sayfasında bu isimle görünür.'),
                        Forms\Components\TextInput::make('question')
                            ->label('Varyasyon sorusu')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Müşteriye sorulan metin.'),
                        Forms\Components\Select::make('answer_type')
                            ->label('Varyasyon cevap şekli')
                            ->options(ExtraVariation::answerTypeOptions())
                            ->required()
                            ->live()
                            ->native(false),
                        Forms\Components\Textarea::make('info_text')
                            ->label('Bilgilendirme metni')
                            ->rows(4)
                            ->required(fn (Get $get): bool => $get('answer_type') === ExtraVariation::TYPE_INFO)
                            ->visible(fn (Get $get): bool => $get('answer_type') === ExtraVariation::TYPE_INFO)
                            ->helperText('Müşteri seçim yapmaz; bu yazıyı okuyup devam eder.'),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Sıra')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->helperText('Aynı varyasyondan sonra birden fazla ekstra varsa küçük sayı önce sorulur.'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ])
                    ->columns(1),
                Forms\Components\Section::make('Cevaplar')
                    ->description('Tekli ve çoklu radio seçenekleri.')
                    ->visible(fn (Get $get): bool => in_array($get('answer_type'), [ExtraVariation::TYPE_RADIO_SINGLE, ExtraVariation::TYPE_RADIO_MULTIPLE], true))
                    ->schema([
                        Forms\Components\Repeater::make('options')
                            ->relationship()
                            ->label('Cevap seçenekleri')
                            ->schema([
                                Forms\Components\TextInput::make('label')
                                    ->label('Cevap')
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->orderColumn('sort_order')
                            ->minItems(1)
                            ->defaultItems(1)
                            ->addActionLabel('Cevap ekle')
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('Ürün ve sıra')
                    ->description('Bu varyasyon hangi ürünlerde, hangi varyasyon seçiminden sonra sorulsun.')
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
                                    ->helperText('Müşteri bu adımdaki seçimini yaptıktan sonra ekstra varyasyon sorulur.')
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
                Tables\Columns\TextColumn::make('question')
                    ->label('Soru')
                    ->wrap()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('answer_type')
                    ->label('Cevap şekli')
                    ->formatStateUsing(fn (string $state): string => ExtraVariation::answerTypeOptions()[$state] ?? $state)
                    ->badge(),
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
        return parent::getEloquentQuery()->withCount('assignments');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExtraVariations::route('/'),
            'create' => Pages\CreateExtraVariation::route('/create'),
            'edit' => Pages\EditExtraVariation::route('/{record}/edit'),
        ];
    }
}
