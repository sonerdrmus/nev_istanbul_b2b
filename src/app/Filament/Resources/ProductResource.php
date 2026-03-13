<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Category;
use App\Models\Currency;
use App\Models\TaxClass;
use App\Models\CustomerGroup;
use App\Models\Product;
use App\Models\ProductVariationOption;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Livewire\Livewire;
use Filament\Tables;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'E-Ticaret';

    protected static ?string $modelLabel = 'Ürün';

    protected static ?string $pluralModelLabel = 'Ürünler';

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('_product_id'),
                Forms\Components\Tabs::make('Ürün Formu')
                    ->columnSpanFull()
                    ->persistTabInQueryString()
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Temel Bilgiler')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Forms\Components\Section::make()
                                    ->schema([
                                        Forms\Components\Select::make('company_id')
                                            ->label('Şirket')
                                            ->relationship('company', 'name')
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->columnSpan(1),
                                        Forms\Components\Select::make('category_id')
                                            ->label('Alt Kategori')
                                            ->options(fn () => Category::whereNotNull('parent_id')->with('parent')->orderBy('sort_order')->orderBy('name')->get()->mapWithKeys(fn ($c) => [$c->id => $c->parent?->name . ' › ' . $c->name])->all())
                                            ->searchable()
                                            ->preload()
                                            ->placeholder('Kategori seçin')
                                            ->nullable()
                                            ->columnSpan(1),
                                        Forms\Components\Select::make('tax_class_id')
                                            ->label('Vergi Sınıfı')
                                            ->relationship('taxClass', 'title', fn ($query) => $query->orderBy('sort_order')->orderBy('title'))
                                            ->searchable()
                                            ->preload()
                                            ->placeholder('Vergi yok')
                                            ->nullable()
                                            ->columnSpan(1),
                                        Forms\Components\TextInput::make('name')
                                            ->label('Ürün Adı')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpan(1),
                                        Forms\Components\TextInput::make('slug')
                                            ->label('URL Slug')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpan(1),
                                        Forms\Components\RichEditor::make('description')
                                            ->label('Açıklama')
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2),
                            ]),
                        Forms\Components\Tabs\Tab::make('SEO')
                            ->icon('heroicon-o-magnifying-glass')
                            ->schema([
                                Forms\Components\Section::make('Meta Etiketleri')
                                    ->description('Arama motorları ve sosyal paylaşımlar için meta bilgileri.')
                                    ->schema([
                                        Forms\Components\TextInput::make('meta_title')
                                            ->label('Meta Tag Title')
                                            ->maxLength(255)
                                            ->columnSpanFull(),
                                        Forms\Components\Textarea::make('meta_description')
                                            ->label('Meta Tag Description')
                                            ->rows(3)
                                            ->maxLength(512)
                                            ->columnSpanFull(),
                                        Forms\Components\TextInput::make('meta_keywords')
                                            ->label('Meta Tag Keywords')
                                            ->placeholder('kelime1, kelime2, kelime3')
                                            ->maxLength(512)
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                        Forms\Components\Tabs\Tab::make('Fiyat & Görsel')
                            ->icon('heroicon-o-currency-dollar')
                            ->schema([
                                Forms\Components\Section::make()
                                    ->schema([
                                        Forms\Components\Select::make('currency_id')
                                            ->label('Para Birimi')
                                            ->relationship('currency', 'name', fn ($query) => $query->active()->orderBy('sort_order')->orderBy('name'))
                                            ->searchable()
                                            ->preload()
                                            ->default(fn () => Currency::getDefault()?->id)
                                            ->placeholder('Varsayılan')
                                            ->live()
                                            ->columnSpan(1),
                                        Forms\Components\TextInput::make('price')
                                            ->label('Fiyat')
                                            ->required()
                                            ->numeric()
                                            ->minValue(0)
                                            ->suffix(fn ($get) => Currency::find($get('currency_id'))?->symbol ?? '₺')
                                            ->columnSpan(1),
                                        Forms\Components\TextInput::make('stock_quantity')
                                            ->label('Stok miktarı')
                                            ->helperText('Boş = stok takibi yok (sınırsız). 0 = stokta yok.')
                                            ->numeric()
                                            ->minValue(0)
                                            ->integer()
                                            ->nullable()
                                            ->columnSpan(1),
                                        Forms\Components\TextInput::make('minimum_order_quantity')
                                            ->label('Minimum sipariş miktarı')
                                            ->helperText('Boş = 1 adet. Müşteri en az bu kadar adet sipariş edebilir.')
                                            ->numeric()
                                            ->minValue(1)
                                            ->integer()
                                            ->nullable()
                                            ->columnSpan(1),
                                        Forms\Components\FileUpload::make('image')
                                            ->label('Ana görsel (liste / öne çıkan)')
                                            ->image()
                                            ->directory('products')
                                            ->visibility('public')
                                            ->imagePreviewHeight('200')
                                            ->nullable()
                                            ->columnSpanFull(),
                                        Forms\Components\Repeater::make('productImages')
                                            ->relationship()
                                            ->label('Ek görseller (ürün sayfasında slayt)')
                                            ->reorderable()
                                            ->reorderableWithButtons()
                                            ->defaultItems(0)
                                            ->addActionLabel('Görsel ekle')
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): ?string => isset($state['path']) ? 'Görsel' : null)
                                            ->schema([
                                                Forms\Components\FileUpload::make('path')
                                                    ->label('Görsel')
                                                    ->image()
                                                    ->directory('products')
                                                    ->visibility('public')
                                                    ->imagePreviewHeight(120)
                                                    ->required(),
                                                Forms\Components\TextInput::make('sort_order')
                                                    ->label('Sıra')
                                                    ->numeric()
                                                    ->minValue(0)
                                                    ->default(0)
                                                    ->columnSpanFull(),
                                            ])
                                            ->columnSpanFull(),
                                        Forms\Components\Toggle::make('is_active')
                                            ->label('Yayında')
                                            ->default(true)
                                            ->inline(false),
                                        Forms\Components\Select::make('status')
                                            ->label('Ürün durumu')
                                            ->options([
                                                'satista' => 'Satışta',
                                                'stokta_yok' => 'Stokta yok',
                                                'yakinda_gelecek' => 'Yakında gelecek',
                                            ])
                                            ->default('satista')
                                            ->required()
                                            ->helperText('Sadece "Satışta" olanlar mağaza listesinde görünür ve sepete eklenebilir.'),
                                    ])
                                    ->columns(2),
                            ]),
                        Forms\Components\Tabs\Tab::make('İndirim')
                            ->icon('heroicon-o-ticket')
                            ->schema([
                                Forms\Components\Section::make('Miktar / Tarih İndirimleri')
                                    ->description('Müşteri grubuna, minimum adete ve tarih aralığına göre indirimli fiyat tanımlayın. Öncelik düşük olan kural önce değerlendirilir.')
                                    ->schema([
                                        Forms\Components\Repeater::make('productDiscounts')
                                            ->relationship()
                                            ->reorderable()
                                            ->reorderableWithButtons()
                                            ->defaultItems(0)
                                            ->addActionLabel('Yeni indirim kuralı')
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): ?string => $state['quantity'] ? "Min. {$state['quantity']} adet" : null)
                                            ->schema([
                                                Forms\Components\Select::make('customer_group_id')
                                                    ->label('Müşteri Grubu')
                                                    ->options(CustomerGroup::orderBy('sort_order')->orderBy('name')->pluck('name', 'id'))
                                                    ->required()
                                                    ->searchable()
                                                    ->columnSpan(1),
                                                Forms\Components\TextInput::make('quantity')
                                                    ->label('Miktar (min. adet)')
                                                    ->required()
                                                    ->numeric()
                                                    ->minValue(1)
                                                    ->default(1)
                                                    ->integer()
                                                    ->columnSpan(1),
                                                Forms\Components\TextInput::make('priority')
                                                    ->label('Öncelik')
                                                    ->numeric()
                                                    ->minValue(1)
                                                    ->default(1)
                                                    ->integer()
                                                    ->columnSpan(1),
                                                Forms\Components\TextInput::make('price')
                                                    ->label('İndirimli fiyat')
                                                    ->required()
                                                    ->numeric()
                                                    ->minValue(0)
                                                    ->step(0.01)
                                                    ->suffix('₺')
                                                    ->columnSpan(1),
                                                Forms\Components\DatePicker::make('date_start')
                                                    ->label('Başlangıç tarihi')
                                                    ->nullable()
                                                    ->columnSpan(1),
                                                Forms\Components\DatePicker::make('date_end')
                                                    ->label('Bitiş tarihi')
                                                    ->nullable()
                                                    ->columnSpan(1),
                                            ])
                                            ->columns(2),
                                    ]),
                            ]),
                        Forms\Components\Tabs\Tab::make('Varyasyonlar')
                            ->icon('heroicon-o-squares-2x2')
                            ->schema([
                                Forms\Components\Section::make('Varyasyonlar')
                                    ->description('Renk, beden vb. varyasyonlar tanımlayın. Her varyasyonun altına birden fazla seçenek ekleyebilir ve seçeneklere görsel yükleyebilirsiniz.')
                                    ->schema([
                                        Forms\Components\Repeater::make('variations')
                                            ->relationship()
                                            ->reorderable()
                                            ->reorderableWithButtons()
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? 'Varyasyon')
                                            ->addActionLabel('Varyasyon ekle')
                                            ->schema([
                                                Forms\Components\TextInput::make('name')
                                                    ->label('Varyasyon adı')
                                                    ->placeholder('Örn: Renk, Beden')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->columnSpan(1),
                                                Forms\Components\Select::make('type')
                                                    ->label('Tip')
                                                    ->options([
                                                        'select' => 'Select',
                                                        'color' => 'Renk',
                                                        'image' => 'Görsel',
                                                    ])
                                                    ->default('select')
                                                    ->required()
                                                    ->columnSpan(1),
                                                Forms\Components\Select::make('depends_on')
                                                    ->label('Bağlı olduğu varyasyon')
                                                    ->placeholder('Bağımsız (boş bırakın)')
                                                    ->options(function (Get $get): array {
                                                        $component = Livewire::current();
                                                        if ($component && method_exists($component, 'getRecord')) {
                                                            $record = $component->getRecord();
                                                            if ($record instanceof Product && $record->exists) {
                                                                return $record->variations()->orderBy('sort_order')->pluck('name', 'name')->all();
                                                            }
                                                        }
                                                        $productId = $get('_product_id') ?? $get('id');
                                                        if ($productId && $product = Product::find($productId)) {
                                                            return $product->variations()->orderBy('sort_order')->pluck('name', 'name')->all();
                                                        }
                                                        $variations = $get('variations');
                                                        if (is_array($variations)) {
                                                            $out = [];
                                                            foreach (collect($variations)->pluck('name')->filter()->unique() as $n) {
                                                                if ($n !== null && $n !== '') {
                                                                    $out[$n] = $n;
                                                                }
                                                            }
                                                            return $out;
                                                        }
                                                        return [];
                                                    })
                                                    ->searchable()
                                                    ->live()
                                                    ->nullable()
                                                    ->helperText('Bu varyasyon hangi varyasyona bağlı? Önce üst varyasyonu ekleyin; kendi adınızı seçmeyin.')
                                                    ->columnSpan(1),
                                                Forms\Components\TextInput::make('sort_order')
                                                    ->label('Sıra')
                                                    ->numeric()
                                                    ->minValue(0)
                                                    ->default(0)
                                                    ->columnSpan(1),
                                                Forms\Components\Repeater::make('options')
                                                    ->relationship()
                                                    ->reorderable()
                                                    ->reorderableWithButtons()
                                                    ->collapsible()
                                                    ->itemLabel(fn (array $state): ?string => $state['option_value'] ?? 'Seçenek')
                                                    ->addActionLabel('Seçenek ekle')
                                                    ->schema([
                                                        Forms\Components\TextInput::make('option_value')
                                                            ->label('Seçenek değeri')
                                                            ->placeholder('Örn: Kırmızı, XL')
                                                            ->required()
                                                            ->maxLength(255)
                                                            ->columnSpan(1),
                                                        Forms\Components\TextInput::make('option_color')
                                                            ->label('Renk (hex)')
                                                            ->placeholder('#ff0000')
                                                            ->maxLength(20)
                                                            ->nullable()
                                                            ->columnSpan(1),
                                                        Forms\Components\FileUpload::make('option_image')
                                                            ->label('Görsel')
                                                            ->image()
                                                            ->directory('variation_options')
                                                            ->visibility('public')
                                                            ->imagePreviewHeight(80)
                                                            ->nullable()
                                                            ->columnSpanFull(),
                                                        Forms\Components\Select::make('option_image_size')
                                                            ->label('Görsel boyutu (ürün sayfasında)')
                                                            ->options([
                                                                'small' => 'Küçük',
                                                                'medium' => 'Orta',
                                                                'large' => 'Büyük',
                                                            ])
                                                            ->default('medium')
                                                            ->nullable()
                                                            ->helperText('Varyasyon görseli mağaza ürün sayfasında bu boyutta gösterilir.')
                                                            ->columnSpan(1),
                                                        Forms\Components\TextInput::make('price_delta')
                                                            ->label('Fiyat farkı (₺)')
                                                            ->numeric()
                                                            ->default(0)
                                                            ->step(0.01)
                                                            ->columnSpan(1),
                                                        Forms\Components\TextInput::make('stock_quantity')
                                                            ->label('Stok miktarı')
                                                            ->numeric()
                                                            ->minValue(0)
                                                            ->integer()
                                                            ->nullable()
                                                            ->columnSpan(1),
                                                        Forms\Components\Select::make('parent_option_ids')
                                                            ->label('Bağlı seçenekler (üst varyasyondan)')
                                                            ->multiple()
                                                            ->options(function (Get $get): array {
                                                                $component = Livewire::current();
                                                                $productId = null;
                                                                if ($component && method_exists($component, 'getRecord')) {
                                                                    $record = $component->getRecord();
                                                                    if ($record instanceof Product && $record->exists) {
                                                                        $productId = $record->getKey();
                                                                    }
                                                                }
                                                                $productId = $productId ?? $get('_product_id') ?? $get('id');
                                                                if (! $productId) {
                                                                    return [];
                                                                }
                                                                return ProductVariationOption::query()
                                                                    ->whereHas('variation', fn ($q) => $q->where('product_id', $productId))
                                                                    ->with('variation')
                                                                    ->orderBy('sort_order')
                                                                    ->get()
                                                                    ->mapWithKeys(fn ($o) => [$o->id => ($o->variation ? $o->variation->name . ' — ' : '') . $o->option_value])
                                                                    ->all();
                                                            })
                                                            ->searchable()
                                                            ->live()
                                                            ->nullable()
                                                            ->helperText('Bu seçenek hangi üst varyasyon seçenek(ler)ine bağlı? Birden fazla seçebilirsiniz.')
                                                            ->columnSpanFull(),
                                                    ])
                                                    ->columns(2)
                                                    ->columnSpanFull(),
                                            ])
                                            ->columns(2)
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Görsel')
                    ->getStateUsing(fn ($record) => $record->productImages->first()?->path ?? $record->image)
                    ->circular()
                    ->defaultImageUrl(fn () => asset('images/logo.png')),
                Tables\Columns\TextColumn::make('company.name')
                    ->label('Şirket')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->formatStateUsing(fn ($state, $record) => $record?->category ? ($record->category->parent ? $record->category->parent->name . ' › ' : '') . $record->category->name : '—')
                    ->sortable(),
                Tables\Columns\TextColumn::make('taxClass.title')
                    ->label('Vergi Sınıfı')
                    ->placeholder('—')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('name')
                    ->label('Ürün Adı')
                    ->searchable()
                    ->sortable()
                    ->weight(\Filament\Support\Enums\FontWeight::SemiBold),
                Tables\Columns\TextColumn::make('price')
                    ->label('Fiyat')
                    ->formatStateUsing(fn ($record) => $record->formatted_price)
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('Stok')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state === null ? '—' : (int) $state),
                Tables\Columns\TextColumn::make('status')
                    ->label('Durum')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'stokta_yok' => 'Stokta yok',
                        'yakinda_gelecek' => 'Yakında gelecek',
                        default => 'Satışta',
                    })
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'stokta_yok' => 'danger',
                        'yakinda_gelecek' => 'warning',
                        default => 'success',
                    }),
                Tables\Columns\TextColumn::make('minimum_order_quantity')
                    ->label('Min. sipariş')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state === null ? '1' : (int) $state)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Yayında')
                    ->boolean(),
            ])
            ->defaultSort('name')
            ->modifyQueryUsing(fn ($query) => $query->with('productImages'))
            ->filters([
                Tables\Filters\SelectFilter::make('company_id')
                    ->label('Şirket')
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('tax_class_id')
                    ->label('Vergi Sınıfı')
                    ->relationship('taxClass', 'title')
                    ->searchable()
                    ->preload()
                    ->placeholder('Tümü'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Yayında'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Henüz ürün yok')
            ->emptyStateDescription('İlk ürünü eklemek için yukarıdaki butonu kullanın.')
            ->emptyStateIcon('heroicon-o-shopping-bag');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

}
