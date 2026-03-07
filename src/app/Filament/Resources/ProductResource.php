<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Category;
use App\Models\Currency;
use App\Models\TaxClass;
use App\Models\CustomerGroup;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
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
                                            ->label('Görsel')
                                            ->image()
                                            ->directory('products')
                                            ->visibility('public')
                                            ->imagePreviewHeight('200')
                                            ->nullable()
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
                                Forms\Components\Section::make()
                                    ->description('Sırayı sürükleyip bırakarak değiştirebilirsiniz. Bağlı varyasyon: önce üst varyasyonu ekleyin, sonra "Bağlı olduğu varyasyon"dan seçin.')
                                    ->schema([
                                        Forms\Components\Repeater::make('variations')
                            ->relationship()
                            ->reorderable()
                            ->reorderableWithButtons()
                            ->defaultItems(0)
                            ->addActionLabel('Varyasyon Ekle')
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                            ->schema([
                                Forms\Components\Section::make('1. Varyasyon bilgisi')
                                    ->icon('heroicon-o-tag')
                                    ->description('Önce varyasyonun adını ve tipini belirleyin, sonra (gerekirse) hangi varyasyona bağlı olduğunu seçin. Aşağıdaki 2. ve 3. adımlar bu bilgilere göre çalışır.')
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Varyasyon Adı')
                                            ->placeholder('Örn: Renk, Beden, Erkek/Bayan')
                                            ->required()
                                            ->maxLength(100)
                                            ->columnSpan(1),
                                        Forms\Components\Select::make('type')
                                            ->label('Tip')
                                            ->options([
                                                'select' => 'Açılır liste',
                                                'checkbox' => 'Checkbox',
                                                'color' => 'Renk (sembol)',
                                                'image' => 'Resim',
                                            ])
                                            ->default('select')
                                            ->required()
                                            ->live()
                                            ->columnSpan(1),
                                        Forms\Components\Select::make('depends_on')
                                            ->label('Bağlı olduğu varyasyon')
                                            ->placeholder('Bağımsız')
                                            ->options(function (Get $get): array {
                                                $items = $get('../../variations') ?? [];
                                                $names = collect($items)->pluck('name')->filter()->unique()->values()->all();
                                                return array_combine($names, $names) ?: [];
                                            })
                                            ->helperText('Önce üst varyasyonu ekleyin, sonra buradan seçin.')
                                            ->nullable()
                                            ->live()
                                            ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                                if (! filled($state)) {
                                                    return;
                                                }
                                                $variations = $get('../../variations') ?? [];
                                                $parent = collect($variations)->first(fn ($v) => ($v['name'] ?? null) === $state);
                                                $parentOptions = collect($parent['options_with_prices'] ?? [])
                                                    ->pluck('option_value')
                                                    ->filter()
                                                    ->values()
                                                    ->all();
                                                $existingRows = collect($get('options_by_parent') ?? [])->keyBy(fn ($r) => (string) ($r['parent_value'] ?? ''));
                                                $rows = [];
                                                foreach ($parentOptions as $pv) {
                                                    $existing = $existingRows->get((string) $pv);
                                                    $rows[] = [
                                                        'parent_value' => (string) $pv,
                                                        'options' => is_array($existing['options'] ?? null) ? $existing['options'] : [],
                                                    ];
                                                }
                                                $set('options_by_parent', $rows);
                                                $set('options_with_prices', []);
                                                $set('options', []);
                                            })
                                            ->columnSpanFull(),
                                        Forms\Components\FileUpload::make('image')
                                            ->label('Varyasyon görseli (opsiyonel)')
                                            ->image()
                                            ->disk('public')
                                            ->directory('product_variations')
                                            ->visibility('public')
                                            ->imagePreviewHeight(80)
                                            ->maxSize(5120)
                                            ->nullable()
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2),
                                Forms\Components\Section::make('2. Seçenekler')
                                    ->icon('heroicon-o-list-bullet')
                                    ->description('Bu varyasyon BAĞIMSIZ ise buradan seçenekleri ve fiyat farklarını ekleyin. Eğer üst varyasyona bağlamak istiyorsanız sadece 1. adımı doldurun ve 3. adıma geçin.')
                                    ->schema([
                                        Forms\Components\Repeater::make('options_with_prices')
                                    ->label('Seçenekler + Fiyat Farkı (₺)')
                                    ->schema([
                                        Forms\Components\TextInput::make('option_value')
                                            ->label('Seçenek')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('option_color')
                                            ->label('Renk (hex)')
                                            ->placeholder('#ff0000')
                                            ->maxLength(20)
                                            ->visible(fn (Get $get) => $get('../../../type') === 'color'),
                                        Forms\Components\FileUpload::make('option_image')
                                            ->label('Örnek görsel')
                                            ->image()
                                            ->disk('public')
                                            ->directory('variation_options')
                                            ->visibility('public')
                                            ->imagePreviewHeight(60)
                                            ->maxSize(5120) // 5 MB
                                            ->helperText('En fazla 5 MB, JPG / PNG / WEBP.')
                                            ->nullable(),
                                        Forms\Components\TextInput::make('price_delta_try')
                                            ->label('Fark (₺)')
                                            ->helperText('Örn: 0, 10.50, -5')
                                            ->numeric()
                                            ->default(0),
                                        Forms\Components\TextInput::make('stock_quantity')
                                            ->label('Stok (opsiyonel)')
                                            ->numeric()
                                            ->minValue(0)
                                            ->integer()
                                            ->nullable(),
                                    ])
                                    ->columns(2)
                                    ->defaultItems(0)
                                    ->addActionLabel('Seçenek Ekle')
                                    ->visible(fn (Get $get) => ! filled($get('depends_on')))
                                    ->required(fn (Get $get) => ! filled($get('depends_on')))
                                    ->afterStateHydrated(function (Set $set, Get $get, $state) {
                                        $variationId = $get('id');
                                        $optionValues = $get('options') ?? [];
                                        $optionValues = is_array($optionValues) ? $optionValues : [];

                                        $priceMap = [];
                                        $stockMap = [];
                                        $optionMeta = [];
                                        if ($variationId) {
                                            $rows = \App\Models\ProductVariationOptionPrice::query()
                                                ->where('product_variation_id', $variationId)
                                                ->get();
                                            $priceMap = $rows->pluck('price_delta_try', 'option_value')->all();
                                            $stockMap = $rows->pluck('stock_quantity', 'option_value')->all();
                                            $variation = \App\Models\ProductVariation::find($variationId);
                                            $optionMeta = $variation?->option_meta ?? [];
                                        }

                                        if (! empty($optionValues)) {
                                            $opts = collect($optionValues)->map(function ($val) use ($priceMap, $stockMap, $optionMeta) {
                                                $val = (string) $val;
                                                $meta = is_array($optionMeta) && isset($optionMeta[$val]) ? $optionMeta[$val] : [];
                                                $meta = is_array($meta) ? $meta : [];
                                                $stock = isset($stockMap[$val]) ? (int) $stockMap[$val] : null;
                                                return [
                                                    'option_value' => $val,
                                                    'option_color' => $meta['color'] ?? null,
                                                    'option_image' => $meta['image'] ?? null,
                                                    'price_delta_try' => isset($priceMap[$val]) ? (float) $priceMap[$val] : 0.0,
                                                    'stock_quantity' => $stock !== null && $stock !== '' ? (int) $stock : null,
                                                ];
                                            })->all();

                                            $set('options_with_prices', $opts);
                                            $set('options', collect($opts)->pluck('option_value')->filter()->values()->all());
                                            return;
                                        }

                                        // Eğer options boşsa ve state doluysa (örneğin tasarım değişikliği sonrası),
                                        // mevcut state'ten migrate et (eski davranışın emniyetli versiyonu)
                                        if (is_array($state) && ! empty($state)) {
                                            $opts = collect($state)->map(fn ($row) => [
                                                'option_value' => (string) ($row['option_value'] ?? ''),
                                                'option_color' => $row['option_color'] ?? null,
                                                'option_image' => $row['option_image'] ?? null,
                                                'price_delta_try' => (float) ($row['price_delta_try'] ?? 0),
                                                'stock_quantity' => isset($row['stock_quantity']) && $row['stock_quantity'] !== '' && $row['stock_quantity'] !== null ? (int) $row['stock_quantity'] : null,
                                            ])->filter(fn ($row) => $row['option_value'] !== '')->values()->all();

                                            $set('options_with_prices', $opts);
                                            $set('options', collect($opts)->pluck('option_value')->filter()->values()->all());
                                        }
                                    })
                                    ->afterStateUpdated(function (Set $set, $state) {
                                        $vals = collect(is_array($state) ? $state : [])->pluck('option_value')->filter()->values()->all();
                                        $set('options', $vals);
                                    })
                                    ->columnSpan(2),

                                // DB'ye yazılacak asıl seçenekler (backward compatible)
                                Forms\Components\Hidden::make('options')
                                    ->dehydrated()
                                    ->default([])
                                    ->columnSpan(2),
                                Forms\Components\Repeater::make('options_by_parent')
                                    ->label('3. Bağımlı seçenekler (üst değere göre)')
                                    ->helperText('Önce bu varyasyonun bağlı olduğu üst varyasyonu yukarıdan seçin. Her satır: üst varyasyondaki bir değer (örn. Erkek) ve bu değere ait alt seçenekler (örn. Sarı, Mavi).')
                                    ->schema([
                                        Forms\Components\Select::make('parent_value')
                                            ->label('Üst değer')
                                            ->placeholder('Üst seçenek seçin')
                                            ->options(function (Get $get): array {
                                                $dependsOn = $get('../../../depends_on');
                                                if (! filled($dependsOn)) {
                                                    return [];
                                                }
                                                $variations = $get('../../../../variations') ?? [];
                                                $parent = collect($variations)->first(fn ($v) => ($v['name'] ?? null) === $dependsOn);
                                                if (! $parent) {
                                                    return [];
                                                }
                                                $vals = collect($parent['options_with_prices'] ?? [])
                                                    ->pluck('option_value')
                                                    ->filter()
                                                    ->unique()
                                                    ->values()
                                                    ->all();
                                                if (empty($vals)) {
                                                    $vals = is_array($parent['options'] ?? null) ? $parent['options'] : [];
                                                }
                                                $vals = array_values(array_unique(array_filter($vals)));
                                                return $vals ? array_combine($vals, $vals) : [];
                                            })
                                            ->searchable()
                                            ->required(),
                                        Forms\Components\Repeater::make('options')
                                            ->label('Seçenekler + Fiyat Farkı (₺)')
                                            ->schema([
                                                Forms\Components\TextInput::make('option_value')
                                                    ->label('Seçenek')
                                                    ->required()
                                                    ->maxLength(255),
                                                Forms\Components\TextInput::make('option_color')
                                                    ->label('Renk (hex)')
                                                    ->placeholder('#ff0000')
                                                    ->maxLength(20)
                                                    ->visible(fn (Get $get) => $get('../../../../type') === 'color'),
                                                Forms\Components\FileUpload::make('option_image')
                                                    ->label('Örnek görsel')
                                                    ->image()
                                                    ->disk('public')
                                                    ->directory('variation_options')
                                                    ->visibility('public')
                                                    ->imagePreviewHeight(60)
                                                    ->maxSize(5120) // 5 MB
                                                    ->helperText('En fazla 5 MB, JPG / PNG / WEBP.')
                                                    ->nullable(),
                                                Forms\Components\TextInput::make('price_delta_try')
                                                    ->label('Fark (₺)')
                                                    ->numeric()
                                                    ->default(0),
                                                Forms\Components\TextInput::make('stock_quantity')
                                                    ->label('Stok (opsiyonel)')
                                                    ->numeric()
                                                    ->minValue(0)
                                                    ->integer()
                                                    ->nullable(),
                                            ])
                                            ->columns(2)
                                            ->defaultItems(0)
                                            ->addActionLabel('Seçenek Ekle')
                                            ->reorderable(false)
                                            ->collapsible(),
                                    ])
                                    ->columns(2)
                                    ->defaultItems(0)
                                    ->addActionLabel('Satır Ekle')
                                    ->visible(fn (Get $get) => filled($get('depends_on')))
                                    ->formatStateUsing(function ($state) {
                                        if ($state === null || (is_array($state) && empty($state))) {
                                            return [];
                                        }
                                        $state = is_object($state) ? (array) $state : $state;
                                        if (! is_array($state)) {
                                            return [];
                                        }
                                        // Zaten form list formatı: [{"parent_value":"Erkek","options":[...]}, ...]
                                        if (array_is_list($state) && isset($state[0]) && is_array($state[0]) && array_key_exists('parent_value', $state[0])) {
                                            return $state;
                                        }
                                        // DB object format: {"Erkek": ["Sarı", "Mavi"], "Kadın": [...]} -> Form list
                                        $list = [];
                                        foreach ($state as $parentValue => $options) {
                                            if (is_numeric((string) $parentValue)) {
                                                continue;
                                            }
                                            $pvStr = is_scalar($parentValue) ? (string) $parentValue : '';
                                            if ($pvStr === '') {
                                                continue;
                                            }
                                            $opts = array_map(function ($o) {
                                                if (is_array($o)) {
                                                    return $o;
                                                }
                                                return ['option_value' => is_scalar($o) ? (string) $o : '', 'price_delta_try' => 0];
                                            }, (array) $options);
                                            $list[] = ['parent_value' => $pvStr, 'options' => $opts];
                                        }
                                        return $list;
                                    })
                                    ->afterStateHydrated(function (Set $set, Get $get, $state) {
                                        $variationId = $get('../../id');
                                        if (! $variationId || ! is_array($state)) {
                                            return;
                                        }
                                        $priceRows = \App\Models\ProductVariationOptionPrice::query()
                                            ->where('product_variation_id', $variationId)
                                            ->get();
                                        $priceMap = $priceRows->pluck('price_delta_try', 'option_value')->all();
                                        $stockMap = $priceRows->pluck('stock_quantity', 'option_value')->all();
                                        $variation = \App\Models\ProductVariation::find($variationId);
                                        $optionMeta = $variation?->option_meta ?? [];

                                        // Object format (DB): {"Erkek": ["S","M","L"], "Kadın": ["36","38"]}
                                        $rows = [];
                                        $mapOpt = function ($o) use ($priceMap, $stockMap, $optionMeta) {
                                            $raw = is_array($o) ? ($o['option_value'] ?? null) : $o;
                                            if ($raw === null) {
                                                $raw = is_array($o) ? ($o[0] ?? '') : $o;
                                            }
                                            $val = is_scalar($raw) ? (string) $raw : '';
                                            $val = trim($val);
                                            $delta = isset($priceMap[$val]) ? (float) $priceMap[$val] : (float) (is_array($o) && isset($o['price_delta_try']) ? $o['price_delta_try'] : 0);
                                            $stock = isset($stockMap[$val]) ? (int) $stockMap[$val] : (is_array($o) && isset($o['stock_quantity']) && $o['stock_quantity'] !== '' && $o['stock_quantity'] !== null ? (int) $o['stock_quantity'] : null);
                                            $meta = is_array($optionMeta) && isset($optionMeta[$val]) ? $optionMeta[$val] : [];
                                            $meta = is_array($meta) ? $meta : [];
                                            return [
                                                'option_value' => $val,
                                                'option_color' => $meta['color'] ?? (is_array($o) ? ($o['option_color'] ?? null) : null),
                                                'option_image' => $meta['image'] ?? (is_array($o) ? ($o['option_image'] ?? null) : null),
                                                'price_delta_try' => $delta,
                                                'stock_quantity' => $stock,
                                            ];
                                        };
                                        $filterEmptyOptions = function (array $opts) use ($mapOpt): array {
                                            $mapped = array_map($mapOpt, $opts);
                                            return array_values(array_filter($mapped, fn ($r) => isset($r['option_value']) && trim((string) $r['option_value']) !== ''));
                                        };
                                        if (! array_is_list($state) || ! isset($state[0]['parent_value'])) {
                                            foreach ($state as $pv => $opts) {
                                                if (is_numeric((string) $pv)) {
                                                    continue;
                                                }
                                                $pvStr = is_scalar($pv) ? (string) $pv : '';
                                                if ($pvStr === '' || trim($pvStr) === '') {
                                                    continue;
                                                }
                                                $opts = is_array($opts) ? $opts : [];
                                                $filtered = $filterEmptyOptions($opts);
                                                if (empty($filtered)) {
                                                    continue;
                                                }
                                                $rows[] = [
                                                    'parent_value' => trim($pvStr),
                                                    'options' => $filtered,
                                                ];
                                            }
                                        } else {
                                            foreach ($state as $row) {
                                                $pv = $row['parent_value'] ?? null;
                                                $opts = $row['options'] ?? [];
                                                $pvStr = is_scalar($pv) ? (string) $pv : '';
                                                if ($pvStr === '' || trim($pvStr) === '') {
                                                    continue;
                                                }
                                                $filtered = $filterEmptyOptions(is_array($opts) ? $opts : []);
                                                if (empty($filtered)) {
                                                    continue;
                                                }
                                                $rows[] = [
                                                    'parent_value' => trim($pvStr),
                                                    'options' => $filtered,
                                                ];
                                            }
                                        }

                                        if (! empty($rows)) {
                                            $set('options_by_parent', $rows);
                                        }
                                    })
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('sort_order')
                                    ->label('Sıra')
                                    ->numeric()
                                    ->default(0)
                                    ->hidden(),
                                    ]),
                            ])
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
                Tables\Columns\TextColumn::make('variations_count')
                    ->label('Varyasyon')
                    ->counts('variations')
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'success' : 'gray'),
            ])
            ->defaultSort('name')
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
