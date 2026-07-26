<?php

namespace App\Filament\Resources;

use App\Filament\Forms\ProductDimensionMultiplierFormSchema;
use App\Filament\Resources\ProductResource\Pages;
use App\Models\Category;
use App\Models\Currency;
use App\Models\CustomerGroup;
use App\Models\InterfaceCertificateVariation;
use App\Models\InterfaceColorVariation;
use App\Models\InterfaceDeliveryMethodVariation;
use App\Models\InterfaceFabricTypeVariation;
use App\Models\InterfaceLabelTypeVariation;
use App\Models\InterfaceMoldModelVariation;
use App\Models\InterfacePackagingPreferenceVariation;
use App\Models\Product;
use App\Models\ProductCustomizationRow;
use App\Models\ProductVariation;
use App\Models\ProductVariationOption;
use App\Models\SizeTable;
use App\Support\ProductVariationFlowSteps;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'E-Ticaret';

    protected static ?string $modelLabel = 'Ürün';

    protected static ?string $pluralModelLabel = 'Ürünler';

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $shouldRegisterNavigation = false;

    /** @var array<int, string>|null */
    protected static ?array $sizeTableOptionsCache = null;

    /** @var array<string, array<int|string, string>> */
    protected static array $parentOptionChoicesCache = [];

    /** @var array<int, array<string, string>> */
    protected static array $productVariationNamesCache = [];

    /** @var array<int, string> */
    protected static array $interfaceColorLabelCache = [];

    /** @var array<int, string> */
    protected static array $interfaceFabricLabelCache = [];

    /** @var array<int, string> */
    protected static array $interfaceLabelTypeLabelCache = [];

    /** @var array<int, string> */
    protected static array $interfacePackagingLabelCache = [];

    /** @var array<int, string> */
    protected static array $interfaceCertificateLabelCache = [];

    /** @var array<int, string> */
    protected static array $interfaceMoldModelLabelCache = [];

    /** @var array<int, string> */
    protected static array $interfaceDeliveryMethodLabelCache = [];

    /** @var array<int, array<int, string>> */
    protected static array $customizationRowOptionsCache = [];

    /**
     * `storage/app/public/<directory>` altında bulunan görselleri listeler.
     * Select için:
     * - value (key): disk üzerindeki göreli path (örn: `products/abc.jpg`)
     * - label: dosya adı
     */
    protected static function getPublicImageSelectOptions(string $directory): array
    {
        $cacheKey = 'public-image-options:'.$directory;
        static $cache = [];
        if (isset($cache[$cacheKey])) {
            return $cache[$cacheKey];
        }

        $paths = Storage::disk('public')->files($directory);
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

        $options = collect($paths)
            ->filter(function (string $path) use ($allowed) {
                $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

                return in_array($ext, $allowed, true);
            })
            ->sort()
            ->mapWithKeys(function (string $path) {
                $label = basename($path);
                $url = asset('storage/'.$path);

                // Filament Select dropdown içinde thumbnail göstermek için HTML label.
                // Search performansı için label sade tutulur.
                $html = '<div class="flex items-center gap-2">'
                    .'<img src="'.e($url).'" alt="'.e($label).'" class="w-7 h-7 rounded-md object-cover border border-slate-200" />'
                    .'<span class="text-sm leading-tight break-all">'.e($label).'</span>'
                    .'</div>';

                return [$path => $html];
            })
            ->all();

        $cache[$cacheKey] = $options;

        return $options;
    }

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
                                            ->label('Kategori')
                                            ->helperText('Ana veya alt kategori seçilebilir. Liste, üst › alt tam yolunu gösterir.')
                                            ->relationship(
                                                'category',
                                                'name',
                                                fn ($query) => $query
                                                    ->with('parentRecursive')
                                                    ->orderBy('sort_order')
                                                    ->orderBy('name'),
                                            )
                                            ->getOptionLabelFromRecordUsing(fn (Category $record): string => $record->full_path)
                                            ->searchable()
                                            ->preload()
                                            ->placeholder('Kategori seçin')
                                            ->nullable()
                                            ->columnSpan(1),
                                        Forms\Components\TextInput::make('sort_order')
                                            ->label('Kategori / listede sıra')
                                            ->helperText('Mağaza kategori ve ürün listelerinde düşük sayı önce gösterilir (aynı kategorideki ürünler arasında).')
                                            ->numeric()
                                            ->integer()
                                            ->minValue(0)
                                            ->default(0)
                                            ->required()
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
                                Forms\Components\Section::make('Anasayfa vitrin (kategoriler bölümü)')
                                    ->description('Açıksa anasayfada kategori kartlarıyla aynı alanda yalnızca ürün görseli ve adı gösterilir; tıklanınca ürün detayına gider. Aşağıdaki vitrin görseli tanımlı değilse ana görsel veya galerideki ilk görsel kullanılır.')
                                    ->schema([
                                        Forms\Components\Toggle::make('show_on_home')
                                            ->label('Anasayfa vitrininde göster')
                                            ->default(false)
                                            ->inline(false),
                                        Forms\Components\TextInput::make('home_showcase_order')
                                            ->label('Vitrinde sıra')
                                            ->helperText('Düşük sayı önce listelenir (kategori kartlarından sonra gelen ürünler arasında).')
                                            ->numeric()
                                            ->integer()
                                            ->minValue(0)
                                            ->default(0)
                                            ->required(),
                                        Forms\Components\Select::make('home_showcase_image')
                                            ->label('Vitrin görseli')
                                            ->helperText('Önerilen oran: kategori vitrin kartlarına uyum için yatay / kare görsel. Boş bırakılırsa ana görsel veya galeri ilk sırası kullanılır.')
                                            ->options(fn () => array_merge(
                                                self::getPublicImageSelectOptions('products'),
                                                self::getPublicImageSelectOptions('products/home-showcase'),
                                            ))
                                            ->allowHtml()
                                            ->searchable()
                                            ->preload()
                                            ->placeholder('Mevcut görsellerden seçin')
                                            ->nullable()
                                            ->columnSpanFull(),
                                        Forms\Components\FileUpload::make('home_showcase_image_upload')
                                            ->label('Vitrin görseli yükle')
                                            ->image()
                                            ->directory('products/home-showcase')
                                            ->visibility('public')
                                            ->imagePreviewHeight(160)
                                            ->maxFiles(1)
                                            ->nullable()
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2)
                                    ->collapsible(),
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
                                            ->helperText('Temel ürün fiyatı. Sipariş miktarı çarpanı ve diğer varyasyon çarpanları bu fiyat üzerinden uygulanır.')
                                            ->columnSpan(1),
                                        Forms\Components\Repeater::make('priceTiers')
                                            ->relationship()
                                            ->label('Sipariş miktarına göre çarpan')
                                            ->helperText('Örn. 1–100 adet → ×1; 101–200 adet → ×2. Max boş bırakılırsa üst sınır yoktur. Girilen değer ürün fiyatıyla çarpılır (diğer varyasyon çarpanları gibi).')
                                            ->reorderable()
                                            ->reorderableWithButtons()
                                            ->orderColumn('sort_order')
                                            ->defaultItems(0)
                                            ->addActionLabel('Çarpan aralığı ekle')
                                            ->collapsible()
                                            ->itemLabel(function (array $state): ?string {
                                                $min = $state['min_quantity'] ?? null;
                                                $max = $state['max_quantity'] ?? null;
                                                $mult = $state['price_multiplier'] ?? null;
                                                if ($min === null && $mult === null) {
                                                    return null;
                                                }
                                                $range = ($min ?? '?').'–'.($max !== null && $max !== '' ? $max : '∞');

                                                return $range.' adet → ×'.($mult ?? '—');
                                            })
                                            ->schema([
                                                Forms\Components\TextInput::make('min_quantity')
                                                    ->label('Min adet')
                                                    ->required()
                                                    ->numeric()
                                                    ->integer()
                                                    ->minValue(1)
                                                    ->default(1)
                                                    ->columnSpan(1),
                                                Forms\Components\TextInput::make('max_quantity')
                                                    ->label('Max adet')
                                                    ->numeric()
                                                    ->integer()
                                                    ->minValue(1)
                                                    ->nullable()
                                                    ->helperText('Boş = sınırsız')
                                                    ->columnSpan(1)
                                                    ->rules([
                                                        fn (Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get): void {
                                                            if ($value === null || $value === '') {
                                                                return;
                                                            }
                                                            $min = $get('min_quantity');
                                                            if ($min !== null && $min !== '' && (int) $value < (int) $min) {
                                                                $fail('Max adet, min adetten küçük olamaz.');
                                                            }
                                                        },
                                                    ]),
                                                Forms\Components\TextInput::make('price_multiplier')
                                                    ->label('Fiyat çarpanı (×)')
                                                    ->required()
                                                    ->numeric()
                                                    ->minValue(0)
                                                    ->step(0.01)
                                                    ->default(1)
                                                    ->helperText('1 = ürün fiyatı aynı; 2 girilirse birim fiyat = ürün fiyatı × 2')
                                                    ->columnSpan(1),
                                                Forms\Components\TextInput::make('sort_order')
                                                    ->label('Sıra')
                                                    ->numeric()
                                                    ->integer()
                                                    ->default(0)
                                                    ->minValue(0)
                                                    ->columnSpan(1),
                                            ])
                                            ->columns(2)
                                            ->columnSpanFull(),
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
                                        Forms\Components\Select::make('image')
                                            ->label('Ana görsel (liste / öne çıkan)')
                                            ->options(fn () => self::getPublicImageSelectOptions('products'))
                                            ->allowHtml()
                                            ->searchable()
                                            ->preload()
                                            ->placeholder('Görsel seçin')
                                            ->nullable()
                                            ->columnSpanFull(),
                                        Forms\Components\FileUpload::make('image_upload')
                                            ->label('Yeni görsel yükle')
                                            ->image()
                                            ->directory('products')
                                            ->visibility('public')
                                            ->imagePreviewHeight(200)
                                            ->maxFiles(1)
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
                                    ->description('Renk, beden vb. varyasyonlar tanımlayın. Performans için kayıtlar varsayılan olarak kapalı gelir; düzenlemek istediğiniz varyasyonu açın.')
                                    ->schema([
                                        Forms\Components\Repeater::make('variations')
                                            ->relationship()
                                            ->orderColumn('sort_order')
                                            ->reorderable()
                                            ->reorderableWithButtons()
                                            ->collapsible()
                                            ->collapsed()
                                            ->itemLabel(function (array $state): ?string {
                                                $name = trim((string) ($state['name'] ?? ''));
                                                $label = $name !== '' ? $name : 'Varyasyon';
                                                $optionCount = is_array($state['options'] ?? null) ? count($state['options']) : 0;
                                                if ($optionCount > 0) {
                                                    $label .= ' ('.$optionCount.' seçenek)';
                                                }
                                                $type = (string) ($state['type'] ?? '');
                                                if (in_array($type, ['select', 'color', 'fabric', 'image', 'label_type', 'certificate_type', 'mold_model_type', 'delivery_type'], true)) {
                                                    $label .= ! empty($state['allows_multiple']) ? ' · çoklu seçim' : ' · tek seçim';
                                                } elseif (! empty($state['allows_multiple'])) {
                                                    $label .= ' · çoklu';
                                                }

                                                return $label;
                                            })
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
                                                        'fabric' => 'Kumaş Türü',
                                                        'label_type' => 'Etiket Türü',
                                                        'certificate_type' => 'Sertifika Türü',
                                                        'mold_model_type' => 'Kalıp Modeli',
                                                        'delivery_type' => 'Teslimat Seçenekleri',
                                                        'packaging_type' => 'Ambalaj Türü',
                                                        'image' => 'Görsel',
                                                        'size_table' => 'Beden Tablosu',
                                                    ])
                                                    ->default('select')
                                                    ->required()
                                                    ->live(debounce: 400)
                                                    ->afterStateUpdated(function ($state, Set $set, $livewire): void {
                                                        $rows = static::interfacePresetOptionRowsForType(
                                                            (string) $state,
                                                            static::currentProductIdFromLivewire($livewire),
                                                        );
                                                        if ($rows !== null) {
                                                            $set('options', $rows);
                                                            if (static::variationTypeRequiresSingleSelectOnly((string) $state)) {
                                                                $set('allows_multiple', false);
                                                            }
                                                            if ($rows === []) {
                                                                $titles = [
                                                                    'fabric' => ['Bu ürüne ait kumaş bulunamadı', 'Varyasyon yönetimi → Kumaş Türü Varyasyonları bölümünde kayıt ekleyin ve «Ürünler» alanından bu ürünü seçin.'],
                                                                    'color' => ['Renk kaydı bulunamadı', 'Önce Varyasyon yönetimi → Renk Varyasyonları bölümünden görseli olan aktif kayıtlar ekleyin.'],
                                                                    'label_type' => ['Etiket türü kaydı bulunamadı', 'Önce Varyasyon yönetimi → Etiket Türü Yönetimi bölümünden kayıt ekleyin.'],
                                                                    'certificate_type' => ['Sertifika kaydı bulunamadı', 'Önce Varyasyon yönetimi → Sertifika Yönetimi bölümünden kayıt ekleyin.'],
                                                                    'mold_model_type' => ['Kalıp modeli kaydı bulunamadı', 'Önce Varyasyon yönetimi → Kalıp Modeli Yönetimi bölümünden kayıt ekleyin.'],
                                                                    'delivery_type' => ['Teslim şekli kaydı bulunamadı', 'Önce Varyasyon yönetimi → Teslim Şeklini Yönet bölümünden kayıt ekleyin.'],
                                                                    'packaging_type' => ['Ambalaj tercihi kaydı bulunamadı', 'Önce Varyasyon yönetimi → Ambalaj Tercih Yönetimi → Ambalaj seç bölümünden kayıt ekleyin.'],
                                                                    'size_table' => ['Beden tablosu bulunamadı', 'Önce Varyasyon yönetimi → Beden tabloları bölümünden en az bir tablo tanımlayın.'],
                                                                ];
                                                                if (isset($titles[$state])) {
                                                                    Notification::make()
                                                                        ->warning()
                                                                        ->title($titles[$state][0])
                                                                        ->body($titles[$state][1])
                                                                        ->send();
                                                                }
                                                            }

                                                            return;
                                                        }
                                                    })
                                                    ->helperText(fn (Get $get): ?string => match ($get('type')) {
                                                        'fabric' => 'Seçenekler yalnızca Varyasyon yönetimi → Kumaş Türü Varyasyonları → Ürünler alanından bu ürüne atanmış kumaşlardan otomatik doldurulur ve güncel tutulur; fiyat çarpanı preset’ten gelir.',
                                                        'label_type' => 'Seçenekler, Etiket Türü Yönetimi kayıtlarından otomatik doldurulur (mevcut seçenek satırlarının yerine geçer).',
                                                        'certificate_type' => 'Seçenekler, Sertifika Yönetimi kayıtlarından otomatik doldurulur; fiyat çarpanı preset’ten gelir.',
                                                        'mold_model_type' => 'Seçenekler, Kalıp Modeli Yönetimi kayıtlarından otomatik doldurulur; fiyat çarpanı preset’ten gelir.',
                                                        'delivery_type' => 'Seçenekler, Teslim Şeklini Yönet kayıtlarından otomatik doldurulur; açıklama ve fiyat çarpanı preset’ten gelir.',
                                                        'packaging_type' => 'Seçenekler, Ambalaj Tercih Yönetimi → Ambalaj seç kayıtlarından otomatik doldurulur; malzeme ve özelleştirme mağazada alt adımlarda sorulur.',
                                                        'color' => 'Seçenekler, Renk varyasyonları kayıtlarından otomatik doldurulur; kumaş türü grubuna göre sıralanır (mevcut seçenek satırlarının yerine geçer).',
                                                        'size_table' => 'Seçenekler, Beden tabloları kayıtlarından otomatik doldurulur. İstemediğiniz tabloları silebilir veya her satırda farklı tablo seçebilirsiniz.',
                                                        default => null,
                                                    })
                                                    ->columnSpan(1),
                                                Forms\Components\Select::make('depends_on')
                                                    ->label('Bağlı olduğu varyasyon')
                                                    ->placeholder('Bağımsız (boş bırakın)')
                                                    ->options(fn (Get $get): array => static::dependsOnVariationOptions($get))
                                                    ->searchable()
                                                    ->live(debounce: 400)
                                                    ->nullable()
                                                    ->helperText('Bu varyasyon hangi adıma bağlı? Ürün varyasyonları veya (açıksa) Ürün özelleştirme adımı seçilebilir.')
                                                    ->columnSpan(1),
                                                Forms\Components\Select::make('depends_on_option_ids')
                                                    ->label('Bağlı olduğu seçenekler')
                                                    ->multiple()
                                                    ->options(function (Get $get): array {
                                                        $dependsOn = trim((string) ($get('depends_on') ?? ''));

                                                        return static::resolveParentVariationOptionChoices($get, $dependsOn);
                                                    })
                                                    ->searchable()
                                                    ->nullable()
                                                    ->visible(fn (Get $get): bool => filled($get('depends_on')))
                                                    ->helperText(fn (Get $get): string => ProductVariationFlowSteps::isCustomizationDependency((string) ($get('depends_on') ?? ''))
                                                        ? 'Ürün özelleştirmede seçilen konum(lar)a göre bu adım görünür. Boş bırakırsanız özelleştirme tamamlandığında görünür.'
                                                        : 'Üst varyasyondaki hangi seçeneklerde bu adım görünsün? Boş bırakırsanız her seçimde görünür (ör. yalnızca Erkek, Çocuk).')
                                                    ->columnSpan(1),
                                                Forms\Components\TextInput::make('sort_order')
                                                    ->label('Sıra')
                                                    ->numeric()
                                                    ->minValue(0)
                                                    ->default(0)
                                                    ->columnSpan(1),
                                                Forms\Components\Textarea::make('info_text')
                                                    ->label('Mağaza açıklama metni')
                                                    ->helperText('Opsiyonel. Varyasyon genelinde gösterilir. Seçenek bazlı metin için aşağıdaki seçenek satırlarını kullanın.')
                                                    ->rows(3)
                                                    ->maxLength(2000)
                                                    ->nullable()
                                                    ->columnSpanFull(),
                                                Forms\Components\Toggle::make('replace_main_gallery_image')
                                                    ->label('Seçilen seçeneğin görseli sol ürün görselinde gösterilsin')
                                                    ->helperText('Açıksa, müşteri bu varyasyonda seçim yaptığında seçeneğin görseli (varsa) mağazada sol taraftaki ana ürün galerisinin ilk görselinin yerine geçer. Birden fazla varyasyonda işaretliyse küçük sıra numarası önceliklidir.')
                                                    ->default(false)
                                                    ->visible(fn (Get $get): bool => ($get('type') ?? '') !== 'size_table')
                                                    ->columnSpanFull(),
                                                Forms\Components\Section::make('Seçim modu')
                                                    ->description(fn (Get $get): string => match ((string) ($get('type') ?? '')) {
                                                        'select' => 'Select tipinde müşterinin tek veya çoklu seçim yapıp yapamayacağını belirleyin.',
                                                        'label_type' => 'Çoklu seçimde müşteri birden fazla etiket seçer; her etiket için alt adımlar sırayla sorulur.',
                                                        default => 'Müşteri bu varyasyonda kaç seçenek seçebilir?',
                                                    })
                                                    ->visible(fn (Get $get): bool => static::variationTypeShowsSelectionModeSection((string) ($get('type') ?? '')))
                                                    ->schema([
                                                        Forms\Components\ToggleButtons::make('allows_multiple')
                                                            ->label('Seçim kuralı')
                                                            ->options([
                                                                0 => 'Tek seçim (zorunlu)',
                                                                1 => 'Birden fazla seçilebilir',
                                                            ])
                                                            ->icons([
                                                                0 => 'heroicon-o-check-circle',
                                                                1 => 'heroicon-o-squares-plus',
                                                            ])
                                                            ->colors([
                                                                0 => 'primary',
                                                                1 => 'success',
                                                            ])
                                                            ->default(0)
                                                            ->inline()
                                                            ->grouped()
                                                            ->live()
                                                            ->afterStateHydrated(function (Forms\Components\ToggleButtons $component, $state): void {
                                                                $component->state(($state === true || $state === 1 || $state === '1') ? 1 : 0);
                                                            })
                                                            ->dehydrateStateUsing(fn ($state): bool => $state === 1 || $state === '1' || $state === true)
                                                            ->visible(fn (Get $get): bool => static::variationTypeAllowsConfigurableSelectionMode((string) ($get('type') ?? '')))
                                                            ->helperText('Tek seçim: otomatik sonraki adım. Çoklu seçim: «Devam et» ile ilerlenir.')
                                                            ->columnSpanFull(),
                                                        Forms\Components\ViewField::make('selection_mode_locked_preview')
                                                            ->label('Seçim kuralı')
                                                            ->view('filament.forms.components.selection-mode-locked')
                                                            ->visible(fn (Get $get): bool => static::variationTypeRequiresSingleSelectOnly((string) ($get('type') ?? '')))
                                                            ->helperText(fn (Get $get): string => static::variationSelectionModeLockedHint((string) ($get('type') ?? '')))
                                                            ->columnSpanFull(),
                                                        Forms\Components\TextInput::make('solo_option_value')
                                                            ->label('Tek başına seçim seçeneği (isteğe bağlı)')
                                                            ->placeholder('Örn: İstemiyorum')
                                                            ->maxLength(255)
                                                            ->nullable()
                                                            ->visible(fn (Get $get): bool => in_array($get('allows_multiple'), [true, 1, '1'], true)
                                                                && ! static::variationTypeRequiresSingleSelectOnly((string) ($get('type') ?? '')))
                                                            ->helperText('Çoklu seçim modunda: bu metinle birebir eşleşen seçenek seçilince diğer işaretler kalkar ve adım otomatik tamamlanır. Boş bırakılırsa solo davranış olmaz.')
                                                            ->columnSpanFull(),
                                                    ])
                                                    ->columns(1)
                                                    ->columnSpanFull(),
                                                Forms\Components\Repeater::make('options')
                                                    ->relationship()
                                                    ->orderColumn('sort_order')
                                                    ->reorderable()
                                                    ->reorderableWithButtons()
                                                    ->collapsible()
                                                    ->collapsed()
                                                    ->itemLabel(fn (array $state): ?string => $state['option_value'] ?? 'Seçenek')
                                                    ->addActionLabel('Seçenek ekle')
                                                    ->addable(fn (Get $get): bool => ! in_array($get('../../type') ?? '', ['fabric', 'color', 'label_type', 'certificate_type', 'mold_model_type', 'delivery_type', 'packaging_type', 'size_table'], true))
                                                    ->schema([
                                                        Forms\Components\Select::make('size_table_id')
                                                            ->label('Beden tablosu')
                                                            ->visible(fn (Get $get): bool => ($get('../../type') ?? '') === 'size_table')
                                                            ->getSearchResultsUsing(fn (string $search): array => static::searchSizeTableOptions($search))
                                                            ->getOptionLabelUsing(fn ($value): ?string => static::sizeTableOptionLabel($value))
                                                            ->searchable()
                                                            ->required(fn (Get $get): bool => ($get('../../type') ?? '') === 'size_table')
                                                            ->live(debounce: 400)
                                                            ->afterStateUpdated(function ($state, Set $set): void {
                                                                if ($state === null || $state === '') {
                                                                    return;
                                                                }
                                                                $table = SizeTable::find((int) $state);
                                                                if (! $table) {
                                                                    return;
                                                                }
                                                                $label = trim((string) ($table->title ?: $table->name ?: ''));
                                                                $set('option_value', $label !== '' ? $label : $table->slug);
                                                            })
                                                            ->helperText('Varyasyon yönetimi → Beden tabloları listesinden seçin.')
                                                            ->columnSpanFull(),
                                                        Forms\Components\Hidden::make('option_value')
                                                            ->visible(fn (Get $get): bool => ($get('../../type') ?? '') === 'size_table'),
                                                        Forms\Components\TextInput::make('option_value')
                                                            ->label('Seçenek değeri')
                                                            ->placeholder('Örn: Kırmızı, XL')
                                                            ->required(fn (Get $get): bool => ($get('../../type') ?? '') !== 'size_table')
                                                            ->maxLength(255)
                                                            ->visible(fn (Get $get): bool => ($get('../../type') ?? '') !== 'size_table')
                                                            ->columnSpan(1),
                                                        Forms\Components\Textarea::make('info_text')
                                                            ->label('Mağaza açıklama metni (seçenek)')
                                                            ->helperText('Opsiyonel. Bu seçenek seçildiğinde mağazada gösterilir; metin varsa müşteri Devam et ile ilerler.')
                                                            ->rows(2)
                                                            ->maxLength(2000)
                                                            ->nullable()
                                                            ->columnSpanFull(),
                                                        Forms\Components\Select::make('interface_color_variation_id')
                                                            ->label('Kayıtlı renk görseli (Arayüz)')
                                                            ->visible(fn (Get $get): bool => ($get('../../type') ?? '') === 'color')
                                                            ->getSearchResultsUsing(fn (string $search): array => static::searchInterfaceColorOptions($search))
                                                            ->getOptionLabelUsing(fn ($value): ?string => static::interfaceColorOptionLabel($value))
                                                            ->searchable()
                                                            ->nullable()
                                                            ->live(debounce: 400)
                                                            ->afterStateUpdated(function ($state, Set $set): void {
                                                                if ($state === null || $state === '') {
                                                                    return;
                                                                }
                                                                $preset = InterfaceColorVariation::find((int) $state);
                                                                if (! $preset) {
                                                                    return;
                                                                }
                                                                $label = trim((string) ($preset->name ?? ''));
                                                                $set('option_value', $label !== '' ? $label : ('#'.$preset->id));
                                                                if (is_string($preset->image_path) && $preset->image_path !== '') {
                                                                    $set('option_image', [$preset->image_path]);
                                                                }
                                                            })
                                                            ->helperText('Varyasyon yönetimi → Renk Varyasyonlarından tanımlı görsel seçilir ve aşağıdaki görsel alanı güncellenir. Özel yükleme kullanırsanız preset sıfırlanır.')
                                                            ->columnSpanFull(),
                                                        Forms\Components\Select::make('interface_fabric_type_variation_id')
                                                            ->label('Kayıtlı kumaş görseli (Arayüz)')
                                                            ->visible(fn (Get $get): bool => ($get('../../type') ?? '') === 'fabric')
                                                            ->getSearchResultsUsing(fn (string $search): array => static::searchInterfaceFabricOptions($search))
                                                            ->getOptionLabelUsing(fn ($value): ?string => static::interfaceFabricOptionLabel($value))
                                                            ->searchable()
                                                            ->nullable()
                                                            ->live(debounce: 400)
                                                            ->afterStateUpdated(function ($state, Set $set): void {
                                                                if ($state === null || $state === '') {
                                                                    return;
                                                                }
                                                                $preset = InterfaceFabricTypeVariation::find((int) $state);
                                                                if (! $preset) {
                                                                    return;
                                                                }
                                                                $label = trim((string) ($preset->name ?? ''));
                                                                $set('option_value', $label !== '' ? $label : ('#'.$preset->id));
                                                                $set('price_delta', ProductVariationOption::normalizePriceMultiplier($preset->price_multiplier));
                                                                if (is_string($preset->image_path) && $preset->image_path !== '') {
                                                                    $set('option_image', [$preset->image_path]);
                                                                }
                                                            })
                                                            ->helperText('Varyasyon yönetimi → Kumaş Türü Varyasyonlarından görsel seçilir; ad, fiyat çarpanı ve görsel otomatik doldurulur.')
                                                            ->columnSpanFull(),
                                                        Forms\Components\Select::make('interface_label_type_variation_id')
                                                            ->label('Kayıtlı etiket türü')
                                                            ->visible(fn (Get $get): bool => ($get('../../type') ?? '') === 'label_type')
                                                            ->getSearchResultsUsing(fn (string $search): array => static::searchInterfaceLabelTypeOptions($search))
                                                            ->getOptionLabelUsing(fn ($value): ?string => static::interfaceLabelTypeOptionLabel($value))
                                                            ->searchable()
                                                            ->nullable()
                                                            ->live(debounce: 400)
                                                            ->afterStateUpdated(function ($state, Set $set): void {
                                                                if ($state === null || $state === '') {
                                                                    return;
                                                                }
                                                                $preset = InterfaceLabelTypeVariation::find((int) $state);
                                                                if (! $preset) {
                                                                    return;
                                                                }
                                                                $set('option_value', (string) $preset->name);
                                                                if (is_string($preset->image_path) && $preset->image_path !== '') {
                                                                    $set('option_image', [$preset->image_path]);
                                                                }
                                                            })
                                                            ->helperText('Varyasyon yönetimi → Etiket Türü Yönetimi kayıtlarından seçilir; görsel ve ad otomatik doldurulur.')
                                                            ->columnSpanFull(),
                                                        Forms\Components\Select::make('interface_certificate_variation_id')
                                                            ->label('Kayıtlı sertifika türü')
                                                            ->visible(fn (Get $get): bool => ($get('../../type') ?? '') === 'certificate_type')
                                                            ->getSearchResultsUsing(fn (string $search): array => static::searchInterfaceCertificateOptions($search))
                                                            ->getOptionLabelUsing(fn ($value): ?string => static::interfaceCertificateOptionLabel($value))
                                                            ->searchable()
                                                            ->nullable()
                                                            ->live(debounce: 400)
                                                            ->afterStateUpdated(function ($state, Set $set): void {
                                                                if ($state === null || $state === '') {
                                                                    return;
                                                                }
                                                                $preset = InterfaceCertificateVariation::find((int) $state);
                                                                if (! $preset) {
                                                                    return;
                                                                }
                                                                $set('option_value', (string) $preset->name);
                                                                $set('price_delta', ProductVariationOption::normalizePriceMultiplier($preset->price_multiplier));
                                                                if (filled($preset->description)) {
                                                                    $set('info_text', (string) $preset->description);
                                                                }
                                                                if (is_string($preset->image_path) && $preset->image_path !== '') {
                                                                    $set('option_image', [$preset->image_path]);
                                                                }
                                                            })
                                                            ->helperText('Varyasyon yönetimi → Sertifika Yönetimi kayıtlarından seçilir; açıklama, görsel, ad ve fiyat çarpanı otomatik doldurulur.')
                                                            ->columnSpanFull(),
                                                        Forms\Components\Select::make('interface_mold_model_variation_id')
                                                            ->label('Kayıtlı kalıp modeli')
                                                            ->visible(fn (Get $get): bool => ($get('../../type') ?? '') === 'mold_model_type')
                                                            ->getSearchResultsUsing(fn (string $search): array => static::searchInterfaceMoldModelOptions($search))
                                                            ->getOptionLabelUsing(fn ($value): ?string => static::interfaceMoldModelOptionLabel($value))
                                                            ->searchable()
                                                            ->nullable()
                                                            ->live(debounce: 400)
                                                            ->afterStateUpdated(function ($state, Set $set): void {
                                                                if ($state === null || $state === '') {
                                                                    return;
                                                                }
                                                                $preset = InterfaceMoldModelVariation::find((int) $state);
                                                                if (! $preset) {
                                                                    return;
                                                                }
                                                                $set('option_value', (string) $preset->name);
                                                                $set('price_delta', ProductVariationOption::normalizePriceMultiplier($preset->price_multiplier));
                                                                if (is_string($preset->image_path) && $preset->image_path !== '') {
                                                                    $set('option_image', [$preset->image_path]);
                                                                }
                                                            })
                                                            ->helperText('Varyasyon yönetimi → Kalıp Modeli Yönetimi kayıtlarından seçilir; görsel, ad ve fiyat çarpanı otomatik doldurulur.')
                                                            ->columnSpanFull(),
                                                        Forms\Components\Select::make('interface_delivery_method_variation_id')
                                                            ->label('Kayıtlı teslimat seçeneği')
                                                            ->visible(fn (Get $get): bool => ($get('../../type') ?? '') === 'delivery_type')
                                                            ->getSearchResultsUsing(fn (string $search): array => static::searchInterfaceDeliveryMethodOptions($search))
                                                            ->getOptionLabelUsing(fn ($value): ?string => static::interfaceDeliveryMethodOptionLabel($value))
                                                            ->searchable()
                                                            ->nullable()
                                                            ->live(debounce: 400)
                                                            ->afterStateUpdated(function ($state, Set $set): void {
                                                                if ($state === null || $state === '') {
                                                                    return;
                                                                }
                                                                $preset = InterfaceDeliveryMethodVariation::find((int) $state);
                                                                if (! $preset) {
                                                                    return;
                                                                }
                                                                $set('option_value', (string) $preset->name);
                                                                $set('price_delta', ProductVariationOption::normalizePriceMultiplier($preset->price_multiplier));
                                                                if (filled($preset->description)) {
                                                                    $set('info_text', (string) $preset->description);
                                                                }
                                                                if (is_string($preset->image_path) && $preset->image_path !== '') {
                                                                    $set('option_image', [$preset->image_path]);
                                                                }
                                                            })
                                                            ->helperText('Varyasyon yönetimi → Teslim Şeklini Yönet kayıtlarından seçilir; açıklama, görsel, ad ve fiyat çarpanı otomatik doldurulur.')
                                                            ->columnSpanFull(),
                                                        Forms\Components\Select::make('interface_packaging_preference_variation_id')
                                                            ->label('Kayıtlı ambalaj türü')
                                                            ->visible(fn (Get $get): bool => ($get('../../type') ?? '') === 'packaging_type')
                                                            ->getSearchResultsUsing(fn (string $search): array => static::searchInterfacePackagingOptions($search))
                                                            ->getOptionLabelUsing(fn ($value): ?string => static::interfacePackagingOptionLabel($value))
                                                            ->searchable()
                                                            ->nullable()
                                                            ->live(debounce: 400)
                                                            ->afterStateUpdated(function ($state, Set $set): void {
                                                                if ($state === null || $state === '') {
                                                                    return;
                                                                }
                                                                $preset = InterfacePackagingPreferenceVariation::find((int) $state);
                                                                if (! $preset) {
                                                                    return;
                                                                }
                                                                $set('option_value', (string) $preset->name);
                                                                $set('price_delta', 1);
                                                                if (is_string($preset->image_path) && $preset->image_path !== '') {
                                                                    $set('option_image', [$preset->image_path]);
                                                                }
                                                            })
                                                            ->helperText('Ambalaj Tercih Yönetimi → Ambalaj seç kayıtlarından gelir; malzeme/özelleştirme mağazada alt adımlarda sorulur.')
                                                            ->columnSpanFull(),
                                                        Forms\Components\TextInput::make('option_color')
                                                            ->label('Renk (hex)')
                                                            ->placeholder('#ff0000')
                                                            ->maxLength(20)
                                                            ->nullable()
                                                            ->visible(fn (Get $get): bool => ($get('../../type') ?? '') === 'color')
                                                            ->columnSpan(1),
                                                        Forms\Components\FileUpload::make('option_image')
                                                            ->label('Varyasyon görseli')
                                                            ->visible(fn (Get $get): bool => ! in_array($get('../../type') ?? '', ['fabric', 'color', 'label_type', 'certificate_type', 'mold_model_type', 'delivery_type', 'packaging_type', 'size_table'], true))
                                                            ->image()
                                                            ->disk('public')
                                                            ->directory('variation_options')
                                                            ->visibility('public')
                                                            ->imagePreviewHeight(72)
                                                            ->maxFiles(1)
                                                            ->nullable()
                                                            ->formatStateUsing(function ($state): ?array {
                                                                if ($state === null || $state === '') {
                                                                    return null;
                                                                }

                                                                return is_array($state) ? $state : [$state];
                                                            })
                                                            ->helperText('Dosya doğrudan kaydedilir. Kayıtlı renk/kumaş preset’i seçtiyseniz preset görseli buraya yazılır; farklı bir dosya yüklerseniz preset bağlantısı kaldırılır.')
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
                                                            ->label('Fiyat çarpanı (×)')
                                                            ->numeric()
                                                            ->default(1)
                                                            ->minValue(0)
                                                            ->step(0.01)
                                                            ->helperText('1 = temel fiyat aynı kalır; 1,50 girildiğinde birim fiyat × 1,50 olur. 0 veya boş kayıtta 1 kabul edilir.')
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
                                                                $dependsOn = trim((string) ($get('../../depends_on') ?? ''));
                                                                if ($dependsOn === '') {
                                                                    return [];
                                                                }

                                                                return static::resolveParentVariationOptionChoices($get, $dependsOn, '../../');
                                                            })
                                                            ->searchable()
                                                            ->nullable()
                                                            ->visible(fn (Get $get): bool => filled($get('../../depends_on')))
                                                            ->helperText('Bu seçenek hangi üst varyasyon seçeneğinde görünsün? Birden fazla seçebilirsiniz. Boş bırakırsanız tüm üst seçeneklerde görünür.')
                                                            ->columnSpanFull(),
                                                    ])
                                                    ->columns(2)
                                                    ->columnSpanFull(),
                                            ])
                                            ->columns(2)
                                            ->columnSpanFull(),
                                        Forms\Components\Section::make('Mağaza: Ürün özelleştirme')
                                            ->description('Bu üründe özelleştirme adımı, atanmış baskı konumları ve ürüne özel çarpanlar.')
                                            ->schema([
                                                Forms\Components\Toggle::make('customization_enabled')
                                                    ->label('Ürün özelleştirme adımını göster')
                                                    ->default(true)
                                                    ->inline(false)
                                                    ->live()
                                                    ->helperText('Kapalıysa mağazada özelleştirme tablosu hiç görünmez.'),
                                                Forms\Components\Select::make('customization_trigger_variation')
                                                    ->label('Özelleştirme adımı hangi varyasyondan sonra')
                                                    ->placeholder('Tüm varyasyonlar — beden adımından hemen önce')
                                                    ->options(function (Get $get): array {
                                                        $names = collect($get('variations') ?? [])
                                                            ->pluck('name')
                                                            ->map(fn ($n) => trim((string) $n))
                                                            ->filter();
                                                        $livewire = Livewire::current();
                                                        if ($livewire && method_exists($livewire, 'getRecord')) {
                                                            $record = $livewire->getRecord();
                                                            if ($record instanceof Product && $record->exists) {
                                                                $names = $names->merge(
                                                                    $record->variations()->orderBy('sort_order')->pluck('name')
                                                                );
                                                            }
                                                        }
                                                        $current = trim((string) ($get('customization_trigger_variation') ?? ''));
                                                        if ($current !== '' && ! $names->contains($current)) {
                                                            $names->push($current);
                                                        }

                                                        return $names->unique()->filter()->sort()->values()
                                                            ->mapWithKeys(fn (string $n): array => [$n => $n])
                                                            ->all();
                                                    })
                                                    ->searchable()
                                                    ->nullable()
                                                    ->visible(fn (Get $get): bool => (bool) $get('customization_enabled'))
                                                    ->helperText('Boş: müşteri tüm varyasyonları seçtikten sonra, beden tablosundan önce özelleştirme adımı gelir. Bir varyasyon seçerseniz: o varyasyonda seçim yapıldıktan sonra özelleştirme adımı açılır; sonraki varyasyonlar özelleştirmeden sonra devam eder. Varyasyon adı üstteki kayıtlarla birebir aynı olmalıdır.'),
                                                Forms\Components\Select::make('customizationRows')
                                                    ->label('Baskı konumları')
                                                    ->relationship(
                                                        name: 'customizationRows',
                                                        titleAttribute: 'position_name',
                                                        modifyQueryUsing: fn ($query) => $query->where('is_active', true)->orderBy('sort_order')->orderBy('id'),
                                                    )
                                                    ->multiple()
                                                    ->searchable()
                                                    ->native(false)
                                                    ->preload(false)
                                                    ->optionsLimit(40)
                                                    ->placeholder('Konum adı yazarak ara…')
                                                    ->visible(fn (Get $get): bool => (bool) $get('customization_enabled'))
                                                    ->helperText('Varyasyon yönetimi → Ürün Özelleştirme kütüphanesindeki konumlardan seçin. Yazarak arayın; mağazada yalnızca seçili konumlar görünür.'),
                                                Forms\Components\Group::make()
                                                    ->schema([
                                                        ProductDimensionMultiplierFormSchema::section(),
                                                    ])
                                                    ->visible(fn (Get $get): bool => (bool) $get('customization_enabled')),
                                            ])
                                            ->columns(1)
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
                    ->formatStateUsing(fn ($state, $record) => $record?->category ? ($record->category->parent ? $record->category->parent->name.' › ' : '').$record->category->name : '—')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Liste sırası')
                    ->sortable()
                    ->alignEnd(),
                Tables\Columns\IconColumn::make('show_on_home')
                    ->label('Ana vitrin')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('home_showcase_order')
                    ->label('Vitrin sırası')
                    ->sortable()
                    ->alignEnd()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            ->defaultSort('sort_order', 'asc')
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
                Tables\Filters\TernaryFilter::make('show_on_home')
                    ->label('Anasayfa vitrin'),
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

    /**
     * Ürün varyasyonu tipi "Kumaş Türü" olduğunda seçenek satırları için kullanılır.
     *
     * @return array<int, array<string, mixed>>
     */
    /**
     * Ürün varyasyonu tipi "Beden Tablosu" olduğunda seçenek satırları — Arayüz beden tabloları.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function sizeTableVariationOptionsFromPresets(): array
    {
        return SizeTable::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(function (SizeTable $table, int $index): array {
                $label = trim((string) ($table->title ?: $table->name ?: ''));
                if ($label === '') {
                    $label = (string) $table->slug;
                }

                return [
                    'option_value' => $label,
                    'size_table_id' => $table->getKey(),
                    'interface_color_variation_id' => null,
                    'interface_fabric_type_variation_id' => null,
                    'interface_label_type_variation_id' => null,
                    'interface_packaging_preference_variation_id' => null,
                    'interface_certificate_variation_id' => null,
                    'interface_delivery_method_variation_id' => null,
                    'interface_mold_model_variation_id' => null,
                    'option_image' => null,
                    'sort_order' => (int) ($table->sort_order ?? ($index * 10)),
                    'price_delta' => 0,
                    'stock_quantity' => null,
                    'parent_option_id' => null,
                    'parent_option_ids' => null,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>|null
     */
    /**
     * Kayıt öncesi: başka ürünlere özel atanmış kumaş seçenek satırlarını bu üründen düşürür.
     */
    public static function dropForeignFabricOptionsFromProductFormData(array $data, ?int $productId): array
    {
        if (empty($data['variations']) || ! is_array($data['variations'])) {
            return $data;
        }

        $hiddenFabricIds = InterfaceFabricTypeVariation::hiddenIdsForProduct($productId);
        if ($hiddenFabricIds === []) {
            return $data;
        }

        foreach ($data['variations'] as &$variation) {
            if ((string) ($variation['type'] ?? '') !== 'fabric' || ! is_array($variation['options'] ?? null)) {
                continue;
            }

            $variation['options'] = array_values(array_filter(
                $variation['options'],
                function ($option) use ($hiddenFabricIds): bool {
                    $presetId = is_array($option) ? ($option['interface_fabric_type_variation_id'] ?? null) : null;

                    return $presetId === null || ! in_array((int) $presetId, $hiddenFabricIds, true);
                },
            ));
        }
        unset($variation);

        return $data;
    }

    /**
     * Form içinde düzenlenen ürünün id'si (oluşturma ekranında henüz kayıt olmadığı için null).
     */
    public static function currentProductIdFromLivewire(?object $livewire): ?int
    {
        if ($livewire === null || ! method_exists($livewire, 'getRecord')) {
            return null;
        }

        $record = $livewire->getRecord();

        return $record instanceof Product && $record->exists ? (int) $record->getKey() : null;
    }

    public static function interfacePresetOptionRowsForType(string $type, ?int $productId = null): ?array
    {
        return match ($type) {
            'fabric' => static::fabricVariationOptionsFromInterfacePresets($productId),
            'color' => static::colorVariationOptionsFromInterfacePresets(),
            'label_type' => static::labelVariationOptionsFromInterfacePresets(),
            'certificate_type' => static::certificateVariationOptionsFromInterfacePresets(),
            'mold_model_type' => static::moldModelVariationOptionsFromInterfacePresets(),
            'delivery_type' => static::deliveryVariationOptionsFromInterfacePresets(),
            'packaging_type' => static::packagingVariationOptionsFromInterfacePresets(),
            'size_table' => static::sizeTableVariationOptionsFromPresets(),
            default => null,
        };
    }


    /**
     * Kayıt öncesi: preset tiplerinde seçenek satırı yoksa Ambalaj / Etiket vb. kayıtlarından doldurur.
     */
    public static function ensureInterfacePresetOptionsInProductFormData(array $data, ?int $productId = null): array
    {
        if (empty($data['variations']) || ! is_array($data['variations'])) {
            return $data;
        }

        $productId ??= isset($data['_product_id']) ? (int) $data['_product_id'] : null;

        foreach ($data['variations'] as &$variation) {
            $type = (string) ($variation['type'] ?? '');
            if (! in_array($type, ['fabric', 'color', 'label_type', 'certificate_type', 'mold_model_type', 'delivery_type', 'packaging_type', 'size_table'], true)) {
                continue;
            }

            $current = $variation['options'] ?? null;
            if (is_array($current) && $current !== []) {
                continue;
            }

            $rows = static::interfacePresetOptionRowsForType($type, $productId);
            if ($rows !== null && $rows !== []) {
                $variation['options'] = $rows;
            }
        }
        unset($variation);

        return $data;
    }

    /**
     * Ürün varyasyonu tipi "Etiket Türü" olduğunda seçenek satırları — Etiket türü kayıtları.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function labelVariationOptionsFromInterfacePresets(): array
    {
        return InterfaceLabelTypeVariation::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(function (InterfaceLabelTypeVariation $preset, int $index): array {
                return [
                    'option_value' => (string) $preset->name,
                    'interface_label_type_variation_id' => $preset->getKey(),
                    'interface_certificate_variation_id' => null,
                    'interface_delivery_method_variation_id' => null,
                    'interface_mold_model_variation_id' => null,
                    'interface_packaging_preference_variation_id' => null,
                    'interface_color_variation_id' => null,
                    'interface_fabric_type_variation_id' => null,
                    'size_table_id' => null,
                    'option_image' => filled($preset->image_path) ? [$preset->image_path] : null,
                    'sort_order' => (int) ($preset->sort_order ?? ($index * 10)),
                    'price_delta' => 0,
                    'stock_quantity' => null,
                    'parent_option_id' => null,
                    'parent_option_ids' => null,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * Ürün varyasyonu tipi "Sertifika Türü" olduğunda seçenek satırları — Sertifika Yönetimi kayıtları.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function certificateVariationOptionsFromInterfacePresets(): array
    {
        return InterfaceCertificateVariation::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(function (InterfaceCertificateVariation $preset, int $index): array {
                return [
                    'option_value' => (string) $preset->name,
                    'info_text' => filled($preset->description) ? (string) $preset->description : null,
                    'interface_certificate_variation_id' => $preset->getKey(),
                    'interface_delivery_method_variation_id' => null,
                    'interface_mold_model_variation_id' => null,
                    'interface_packaging_preference_variation_id' => null,
                    'interface_label_type_variation_id' => null,
                    'interface_color_variation_id' => null,
                    'interface_fabric_type_variation_id' => null,
                    'size_table_id' => null,
                    'option_image' => filled($preset->image_path) ? [$preset->image_path] : null,
                    'sort_order' => (int) ($preset->sort_order ?? ($index * 10)),
                    'price_delta' => ProductVariationOption::normalizePriceMultiplier($preset->price_multiplier),
                    'stock_quantity' => null,
                    'parent_option_id' => null,
                    'parent_option_ids' => null,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * Ürün varyasyonu tipi "Kalıp Modeli" olduğunda seçenek satırları — Kalıp Modeli Yönetimi kayıtları.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function moldModelVariationOptionsFromInterfacePresets(): array
    {
        return InterfaceMoldModelVariation::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(function (InterfaceMoldModelVariation $preset, int $index): array {
                return [
                    'option_value' => (string) $preset->name,
                    'interface_mold_model_variation_id' => $preset->getKey(),
                    'interface_certificate_variation_id' => null,
                    'interface_delivery_method_variation_id' => null,
                    'interface_packaging_preference_variation_id' => null,
                    'interface_label_type_variation_id' => null,
                    'interface_color_variation_id' => null,
                    'interface_fabric_type_variation_id' => null,
                    'size_table_id' => null,
                    'option_image' => filled($preset->image_path) ? [$preset->image_path] : null,
                    'sort_order' => (int) ($preset->sort_order ?? ($index * 10)),
                    'price_delta' => ProductVariationOption::normalizePriceMultiplier($preset->price_multiplier),
                    'stock_quantity' => null,
                    'parent_option_id' => null,
                    'parent_option_ids' => null,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * Ürün varyasyonu tipi "Teslimat Seçenekleri" olduğunda seçenek satırları — Teslim Şeklini Yönet kayıtları.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function deliveryVariationOptionsFromInterfacePresets(): array
    {
        return InterfaceDeliveryMethodVariation::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(function (InterfaceDeliveryMethodVariation $preset, int $index): array {
                return [
                    'option_value' => (string) $preset->name,
                    'info_text' => filled($preset->description) ? (string) $preset->description : null,
                    'interface_delivery_method_variation_id' => $preset->getKey(),
                    'interface_certificate_variation_id' => null,
                    'interface_mold_model_variation_id' => null,
                    'interface_packaging_preference_variation_id' => null,
                    'interface_label_type_variation_id' => null,
                    'interface_color_variation_id' => null,
                    'interface_fabric_type_variation_id' => null,
                    'size_table_id' => null,
                    'option_image' => filled($preset->image_path) ? [$preset->image_path] : null,
                    'sort_order' => (int) ($preset->sort_order ?? ($index * 10)),
                    'price_delta' => ProductVariationOption::normalizePriceMultiplier($preset->price_multiplier),
                    'stock_quantity' => null,
                    'parent_option_id' => null,
                    'parent_option_ids' => null,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * Ürün varyasyonu tipi "Ambalaj Türü" olduğunda seçenek satırları — Ambalaj tercih kayıtları.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function packagingVariationOptionsFromInterfacePresets(): array
    {
        return InterfacePackagingPreferenceVariation::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(function (InterfacePackagingPreferenceVariation $preset, int $index): array {
                return [
                    'option_value' => (string) $preset->name,
                    'interface_packaging_preference_variation_id' => $preset->getKey(),
                    'interface_certificate_variation_id' => null,
                    'interface_mold_model_variation_id' => null,
                    'interface_delivery_method_variation_id' => null,
                    'interface_label_type_variation_id' => null,
                    'interface_color_variation_id' => null,
                    'interface_fabric_type_variation_id' => null,
                    'size_table_id' => null,
                    'option_image' => filled($preset->image_path) ? [$preset->image_path] : null,
                    'sort_order' => (int) ($preset->sort_order ?? ($index * 10)),
                    'price_delta' => 1,
                    'stock_quantity' => null,
                    'parent_option_id' => null,
                    'parent_option_ids' => null,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * Kumaş seçenek satırları. $productId verildiğinde yalnızca o ürüne açıkça atanmış kumaşlar döner.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function fabricVariationOptionsFromInterfacePresets(?int $productId = null): array
    {
        return InterfaceFabricTypeVariation::query()
            ->where('is_active', true)
            ->visibleForProduct($productId)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(function (InterfaceFabricTypeVariation $preset, int $index): array {
                $label = trim((string) ($preset->name ?? ''));
                if ($label === '') {
                    $label = 'Kumaş #'.$preset->getKey();
                }

                return [
                    'option_value' => $label,
                    'interface_fabric_type_variation_id' => $preset->getKey(),
                    'interface_label_type_variation_id' => null,
                    'interface_certificate_variation_id' => null,
                    'interface_delivery_method_variation_id' => null,
                    'interface_mold_model_variation_id' => null,
                    'interface_packaging_preference_variation_id' => null,
                    'interface_color_variation_id' => null,
                    'option_image' => filled($preset->image_path) ? [$preset->image_path] : null,
                    'sort_order' => (int) ($preset->sort_order ?? ($index * 10)),
                    'price_delta' => ProductVariationOption::normalizePriceMultiplier($preset->price_multiplier),
                    'stock_quantity' => null,
                    'parent_option_id' => null,
                    'parent_option_ids' => null,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * Ürün varyasyonu tipi "Renk" olduğunda seçenek satırları — Arayüz renk kayıtları, kumaş türü grubuna göre sıralı.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function colorVariationOptionsFromInterfacePresets(): array
    {
        $presets = InterfaceColorVariation::query()
            ->where('is_active', true)
            ->whereNotNull('image_path')
            ->where('image_path', '!=', '')
            ->with('fabricTypeVariation')
            ->get();

        $sorted = $presets->sortBy(function (InterfaceColorVariation $c): string {
            $ft = $c->fabricTypeVariation;
            $nullOrGrouped = $ft === null ? '0' : '1';
            $fabricSort = $ft !== null ? (int) ($ft->sort_order ?? 0) : 0;
            $fabricKey = $ft !== null ? (int) $ft->getKey() : 0;
            $colorSort = (int) ($c->sort_order ?? 0);

            return $nullOrGrouped.'-'.sprintf('%08d-%010d-%08d-%010d', $fabricSort, $fabricKey, $colorSort, $c->getKey());
        })->values();

        return $sorted
            ->map(fn (InterfaceColorVariation $preset, int $index): array => static::colorVariationOptionRowFromInterfacePreset($preset, $index * 10))
            ->values()
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    public static function colorVariationOptionRowFromInterfacePreset(InterfaceColorVariation $preset, ?int $fallbackSortOrder = null): array
    {
        $label = trim((string) ($preset->name ?? ''));
        if ($label === '') {
            $label = 'Renk #'.$preset->getKey();
        }

        return [
            'option_value' => $label,
            'interface_color_variation_id' => $preset->getKey(),
            'interface_fabric_type_variation_id' => null,
                    'interface_label_type_variation_id' => null,
                    'interface_certificate_variation_id' => null,
                    'interface_mold_model_variation_id' => null,
                    'interface_delivery_method_variation_id' => null,
                    'interface_packaging_preference_variation_id' => null,
            'option_image' => filled($preset->image_path) ? [$preset->image_path] : null,
            'sort_order' => (int) ($preset->sort_order ?? $fallbackSortOrder ?? 0),
            'price_delta' => 0,
            'stock_quantity' => null,
            'parent_option_id' => null,
            'parent_option_ids' => null,
        ];
    }

    /**
     * Ürünlerin "Kumaş türü" varyasyon seçeneklerini kumaş–ürün atamalarıyla eşitler:
     * yalnızca o ürüne atanmış kumaşlar eklenir, atanmamış olanlar kaldırılır.
     *
     * @param  int|null  $productId  Yalnızca bu ürün için çalıştır (null: tüm ürünler).
     * @param  int|null  $presetId  Yalnızca bu kumaş için çalıştır (null: tüm kumaşlar).
     * @return array{added: int, removed: int}
     */
    public static function reconcileFabricOptionsForProducts(?int $productId = null, ?int $presetId = null): array
    {
        $fkField = 'interface_fabric_type_variation_id';
        $added = 0;
        $removed = 0;

        $visibleRowsByProduct = [];

        ProductVariation::query()
            ->where('type', 'fabric')
            ->when($productId !== null, fn (Builder $q) => $q->where('product_id', $productId))
            ->each(function (ProductVariation $variation) use (&$added, &$removed, &$visibleRowsByProduct, $fkField, $presetId): void {
                $variationProductId = $variation->product_id !== null ? (int) $variation->product_id : null;
                $cacheKey = $variationProductId ?? 0;

                $visibleRows = $visibleRowsByProduct[$cacheKey]
                    ??= static::fabricVariationOptionsFromInterfacePresets($variationProductId);

                $visibleIds = array_map(fn (array $row): int => (int) $row[$fkField], $visibleRows);

                $removeQuery = $variation->options()->whereNotNull($fkField);
                if ($visibleIds !== []) {
                    $removeQuery->whereNotIn($fkField, $visibleIds);
                }
                if ($presetId !== null) {
                    $removeQuery->where($fkField, $presetId);
                }
                $removed += $removeQuery->delete();

                foreach ($visibleRows as $row) {
                    $rowPresetId = (int) $row[$fkField];
                    if ($presetId !== null && $rowPresetId !== $presetId) {
                        continue;
                    }

                    if ($variation->options()->where($fkField, $rowPresetId)->exists()) {
                        continue;
                    }

                    static::createVariationOptionFromPresetRow($variation, $row, 'fabric');
                    $added++;
                }
            });

        return ['added' => $added, 'removed' => $removed];
    }

    /**
     * Varyasyon yönetimi preset'lerinden eksik ürün seçeneklerini ekler (mevcut satırları silmez).
     */
    public static function appendMissingInterfacePresetOptions(string $variationType, ?int $onlyPresetId = null): int
    {
        // Kumaş seçenekleri ürün bazlıdır; ekleme/kaldırma işini ürüne duyarlı mutabakat yapar.
        if ($variationType === 'fabric') {
            return static::reconcileFabricOptionsForProducts(presetId: $onlyPresetId)['added'];
        }

        $rows = static::interfacePresetOptionRowsForType($variationType);
        if ($rows === null || $rows === []) {
            return 0;
        }

        $fkField = static::interfacePresetForeignKeyForVariationType($variationType);
        if ($fkField === null) {
            return 0;
        }

        if ($onlyPresetId !== null) {
            $rows = array_values(array_filter(
                $rows,
                fn (array $row): bool => (int) ($row[$fkField] ?? 0) === $onlyPresetId,
            ));
            if ($rows === []) {
                return 0;
            }
        }

        $created = 0;

        ProductVariation::query()
            ->where('type', $variationType)
            ->each(function (ProductVariation $variation) use ($rows, $fkField, $variationType, &$created): void {
                foreach ($rows as $row) {
                    $presetId = $row[$fkField] ?? null;
                    if ($presetId === null) {
                        continue;
                    }

                    $exists = $variation->options()->where($fkField, $presetId)->exists();
                    if ($exists) {
                        continue;
                    }

                    static::createVariationOptionFromPresetRow($variation, $row, $variationType);
                    $created++;
                }
            });

        return $created;
    }

    /**
     * Yeni arayüz renk kaydını, mevcut ürün Renk varyasyonlarına eksikse ekler (mevcut seçenekleri silmez).
     */
    public static function appendColorVariationOptionFromInterfacePreset(InterfaceColorVariation $preset): int
    {
        if (! $preset->is_active || ! filled($preset->image_path)) {
            return 0;
        }

        return static::appendMissingInterfacePresetOptions('color', $preset->getKey());
    }

    public static function interfacePresetForeignKeyForVariationType(string $type): ?string
    {
        return match ($type) {
            'fabric' => 'interface_fabric_type_variation_id',
            'color' => 'interface_color_variation_id',
            'label_type' => 'interface_label_type_variation_id',
            'certificate_type' => 'interface_certificate_variation_id',
            'mold_model_type' => 'interface_mold_model_variation_id',
            'delivery_type' => 'interface_delivery_method_variation_id',
            'packaging_type' => 'interface_packaging_preference_variation_id',
            'size_table' => 'size_table_id',
            default => null,
        };
    }

    /**
     * Ürünün "Renk" varyasyonu seçeneklerini Varyasyon yönetimi → Renk Varyasyonları kayıtlarıyla yeniler.
     * Mağazada kumaş türüne göre renk filtrelemesi için interface_color_variation_id gerekir.
     */
    public static function syncColorVariationOptionsForVariation(ProductVariation $variation): int
    {
        if ((string) $variation->type !== 'color') {
            return 0;
        }

        $rows = static::colorVariationOptionsFromInterfacePresets();
        if ($rows === []) {
            return 0;
        }

        $variation->options()->delete();

        $created = 0;
        foreach ($rows as $row) {
            static::createColorVariationOptionFromPresetRow($variation, $row);
            $created++;
        }

        return $created;
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private static function createColorVariationOptionFromPresetRow(ProductVariation $variation, array $row): void
    {
        static::createVariationOptionFromPresetRow($variation, $row, 'color');
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private static function createVariationOptionFromPresetRow(ProductVariation $variation, array $row, string $variationType): void
    {
        $image = $row['option_image'] ?? null;
        if (is_array($image)) {
            $image = $image[0] ?? null;
        }

        ProductVariationOption::query()->create([
            'product_variation_id' => $variation->getKey(),
            'option_value' => $row['option_value'],
            'info_text' => $row['info_text'] ?? null,
            'interface_color_variation_id' => $row['interface_color_variation_id'] ?? null,
            'interface_fabric_type_variation_id' => $row['interface_fabric_type_variation_id'] ?? null,
            'interface_label_type_variation_id' => $row['interface_label_type_variation_id'] ?? null,
            'interface_certificate_variation_id' => $row['interface_certificate_variation_id'] ?? null,
            'interface_mold_model_variation_id' => $row['interface_mold_model_variation_id'] ?? null,
            'interface_delivery_method_variation_id' => $row['interface_delivery_method_variation_id'] ?? null,
            'interface_packaging_preference_variation_id' => $row['interface_packaging_preference_variation_id'] ?? null,
            'size_table_id' => $row['size_table_id'] ?? null,
            'option_image' => is_string($image) && $image !== '' ? $image : null,
            'option_color' => $variationType === 'color' && filled($row['option_color'] ?? null)
                ? (string) $row['option_color']
                : null,
            'sort_order' => (int) ($row['sort_order'] ?? 0),
            'price_delta' => (float) ($row['price_delta'] ?? 0),
            'stock_quantity' => $row['stock_quantity'] ?? null,
            'parent_option_id' => $row['parent_option_id'] ?? null,
            'parent_option_ids' => $row['parent_option_ids'] ?? null,
        ]);
    }

    /**
     * Varyasyon seçeneklerinde yüklenen görsel ve Arayüz renk / kumaş preset’lerini kayıt öncesi normalize eder.
     */
    public static function finalizeVariationOptionsInProductFormData(array $data, ?int $productId = null): array
    {
        $productId ??= isset($data['_product_id']) ? (int) $data['_product_id'] : null;

        $data = static::ensureInterfacePresetOptionsInProductFormData($data, $productId);
        $data = static::dropForeignFabricOptionsFromProductFormData($data, $productId);

        if (empty($data['variations']) || ! is_array($data['variations'])) {
            return $data;
        }

        foreach ($data['variations'] as $variationIndex => &$variation) {
            $variation['sort_order'] = (int) $variationIndex * 10;
            $variationType = (string) ($variation['type'] ?? '');

            if (static::variationTypeRequiresSingleSelectOnly($variationType)) {
                $variation['allows_multiple'] = false;
            }

            if (empty($variation['options']) || ! is_array($variation['options'])) {
                continue;
            }

            foreach ($variation['options'] as $optionIndex => &$opt) {
                $opt['sort_order'] = (int) $optionIndex * 10;
                unset($opt['option_image_upload']);

                if (isset($opt['option_image']) && is_array($opt['option_image'])) {
                    $opt['option_image'] = $opt['option_image'][0] ?? null;
                }

                if ($variationType !== 'color') {
                    $opt['interface_color_variation_id'] = null;
                }
                if ($variationType !== 'fabric') {
                    $opt['interface_fabric_type_variation_id'] = null;
                }
                if ($variationType !== 'label_type') {
                    $opt['interface_label_type_variation_id'] = null;
                }
                if ($variationType !== 'certificate_type') {
                    $opt['interface_certificate_variation_id'] = null;
                }
                if ($variationType !== 'mold_model_type') {
                    $opt['interface_mold_model_variation_id'] = null;
                }
                if ($variationType !== 'delivery_type') {
                    $opt['interface_delivery_method_variation_id'] = null;
                }
                if ($variationType !== 'packaging_type') {
                    $opt['interface_packaging_preference_variation_id'] = null;
                }
                if ($variationType !== 'size_table') {
                    $opt['size_table_id'] = null;
                }

                if ($variationType === 'size_table' && ! empty($opt['size_table_id'])) {
                    $table = SizeTable::find((int) $opt['size_table_id']);
                    if ($table) {
                        $label = trim((string) ($table->title ?: $table->name ?: ''));
                        $opt['option_value'] = $label !== '' ? $label : (string) $table->slug;
                    }
                }

                $img = isset($opt['option_image']) ? trim((string) $opt['option_image']) : '';

                if ($variationType === 'color' && ! empty($opt['interface_color_variation_id'])) {
                    $preset = InterfaceColorVariation::find((int) $opt['interface_color_variation_id']);
                    if ($preset) {
                        $pPath = trim((string) ($preset->image_path ?? ''));
                        if ($img !== '' && $pPath !== '' && $img !== $pPath) {
                            $opt['interface_color_variation_id'] = null;
                        } else {
                            if (trim((string) ($opt['option_value'] ?? '')) === '') {
                                $n = trim((string) ($preset->name ?? ''));
                                $opt['option_value'] = $n !== '' ? $n : ('#'.$preset->id);
                            }
                            if ($pPath !== '') {
                                $opt['option_image'] = $pPath;
                            }
                        }
                    }
                } elseif ($variationType === 'fabric' && ! empty($opt['interface_fabric_type_variation_id'])) {
                    $preset = InterfaceFabricTypeVariation::find((int) $opt['interface_fabric_type_variation_id']);
                    if ($preset) {
                        $pPath = trim((string) ($preset->image_path ?? ''));
                        if ($img !== '' && $pPath !== '' && $img !== $pPath) {
                            $opt['interface_fabric_type_variation_id'] = null;
                        } else {
                            if (trim((string) ($opt['option_value'] ?? '')) === '') {
                                $n = trim((string) ($preset->name ?? ''));
                                $opt['option_value'] = $n !== '' ? $n : ('#'.$preset->id);
                            }
                            $opt['price_delta'] = ProductVariationOption::normalizePriceMultiplier($preset->price_multiplier);
                            if ($pPath !== '') {
                                $opt['option_image'] = $pPath;
                            }
                        }
                    }
                } elseif ($variationType === 'label_type' && ! empty($opt['interface_label_type_variation_id'])) {
                    $preset = InterfaceLabelTypeVariation::find((int) $opt['interface_label_type_variation_id']);
                    if ($preset) {
                        $pPath = trim((string) ($preset->image_path ?? ''));
                        if ($img !== '' && $pPath !== '' && $img !== $pPath) {
                            $opt['interface_label_type_variation_id'] = null;
                        } else {
                            if (trim((string) ($opt['option_value'] ?? '')) === '') {
                                $opt['option_value'] = (string) $preset->name;
                            }
                            if ($pPath !== '') {
                                $opt['option_image'] = $pPath;
                            }
                        }
                    }
                } elseif ($variationType === 'certificate_type' && ! empty($opt['interface_certificate_variation_id'])) {
                    $preset = InterfaceCertificateVariation::find((int) $opt['interface_certificate_variation_id']);
                    if ($preset) {
                        $pPath = trim((string) ($preset->image_path ?? ''));
                        if ($img !== '' && $pPath !== '' && $img !== $pPath) {
                            $opt['interface_certificate_variation_id'] = null;
                        } else {
                            if (trim((string) ($opt['option_value'] ?? '')) === '') {
                                $opt['option_value'] = (string) $preset->name;
                            }
                            if (filled($preset->description)) {
                                $opt['info_text'] = (string) $preset->description;
                            }
                            $opt['price_delta'] = ProductVariationOption::normalizePriceMultiplier($preset->price_multiplier);
                            if ($pPath !== '') {
                                $opt['option_image'] = $pPath;
                            }
                        }
                    }
                } elseif ($variationType === 'mold_model_type' && ! empty($opt['interface_mold_model_variation_id'])) {
                    $preset = InterfaceMoldModelVariation::find((int) $opt['interface_mold_model_variation_id']);
                    if ($preset) {
                        $pPath = trim((string) ($preset->image_path ?? ''));
                        if ($img !== '' && $pPath !== '' && $img !== $pPath) {
                            $opt['interface_mold_model_variation_id'] = null;
                        } else {
                            if (trim((string) ($opt['option_value'] ?? '')) === '') {
                                $opt['option_value'] = (string) $preset->name;
                            }
                            $opt['price_delta'] = ProductVariationOption::normalizePriceMultiplier($preset->price_multiplier);
                            if ($pPath !== '') {
                                $opt['option_image'] = $pPath;
                            }
                        }
                    }
                } elseif ($variationType === 'delivery_type' && ! empty($opt['interface_delivery_method_variation_id'])) {
                    $preset = InterfaceDeliveryMethodVariation::find((int) $opt['interface_delivery_method_variation_id']);
                    if ($preset) {
                        $pPath = trim((string) ($preset->image_path ?? ''));
                        if ($img !== '' && $pPath !== '' && $img !== $pPath) {
                            $opt['interface_delivery_method_variation_id'] = null;
                        } else {
                            if (trim((string) ($opt['option_value'] ?? '')) === '') {
                                $opt['option_value'] = (string) $preset->name;
                            }
                            if (filled($preset->description)) {
                                $opt['info_text'] = (string) $preset->description;
                            }
                            $opt['price_delta'] = ProductVariationOption::normalizePriceMultiplier($preset->price_multiplier);
                            if ($pPath !== '') {
                                $opt['option_image'] = $pPath;
                            }
                        }
                    }
                } elseif ($variationType === 'packaging_type' && ! empty($opt['interface_packaging_preference_variation_id'])) {
                    $preset = InterfacePackagingPreferenceVariation::find((int) $opt['interface_packaging_preference_variation_id']);
                    if ($preset) {
                        $pPath = trim((string) ($preset->image_path ?? ''));
                        if ($img !== '' && $pPath !== '' && $img !== $pPath) {
                            $opt['interface_packaging_preference_variation_id'] = null;
                        } else {
                            if (trim((string) ($opt['option_value'] ?? '')) === '') {
                                $opt['option_value'] = (string) $preset->name;
                            }
                            $opt['price_delta'] = 1;
                            if ($pPath !== '') {
                                $opt['option_image'] = $pPath;
                            }
                        }
                    }
                }
            }
        }
        unset($variation, $opt);

        return $data;
    }

    /**
     * İlişkili repeater kaydı sırasında varyasyon seçeneği görseli bazen DB'ye yazılmıyor;
     * kayıt tamamlandıktan sonra form durumundan tekrar senkronize eder.
     */
    public static function syncVariationOptionImagesAfterFilamentSave(Product $product, array $formData): void
    {
        $formData = self::finalizeVariationOptionsInProductFormData($formData, (int) $product->getKey());

        foreach ($formData['variations'] ?? [] as $variationRow) {
            $variationId = isset($variationRow['id']) ? (int) $variationRow['id'] : null;
            if (! $variationId) {
                continue;
            }

            $variation = ProductVariation::query()
                ->where('product_id', $product->getKey())
                ->whereKey($variationId)
                ->first();

            if (! $variation) {
                continue;
            }

            foreach ($variationRow['options'] ?? [] as $optionRow) {
                $optionId = isset($optionRow['id']) ? (int) $optionRow['id'] : null;
                if (! $optionId) {
                    continue;
                }

                $option = ProductVariationOption::query()
                    ->where('product_variation_id', $variation->getKey())
                    ->whereKey($optionId)
                    ->first();

                if (! $option) {
                    continue;
                }

                $path = $optionRow['option_image'] ?? null;
                $path = is_string($path) && trim($path) !== '' ? $path : null;

                $payload = [
                    'option_image' => $path,
                    'interface_color_variation_id' => $optionRow['interface_color_variation_id'] ?? null,
                    'interface_fabric_type_variation_id' => $optionRow['interface_fabric_type_variation_id'] ?? null,
                    'interface_label_type_variation_id' => $optionRow['interface_label_type_variation_id'] ?? null,
                    'interface_certificate_variation_id' => $optionRow['interface_certificate_variation_id'] ?? null,
                    'interface_mold_model_variation_id' => $optionRow['interface_mold_model_variation_id'] ?? null,
                    'interface_delivery_method_variation_id' => $optionRow['interface_delivery_method_variation_id'] ?? null,
                    'interface_packaging_preference_variation_id' => $optionRow['interface_packaging_preference_variation_id'] ?? null,
                    'size_table_id' => $optionRow['size_table_id'] ?? null,
                ];
                $resolvedValue = self::resolveVariationOptionValueForSync($optionRow, $variationRow);
                if ($resolvedValue !== null) {
                    $payload['option_value'] = $resolvedValue;
                }
                $option->update($payload);
            }
        }
    }

    /**
     * @param  array<string, mixed>  $optionRow
     * @param  array<string, mixed>  $variationRow
     */
    private static function resolveVariationOptionValueForSync(array $optionRow, array $variationRow): ?string
    {
        $value = trim((string) ($optionRow['option_value'] ?? ''));
        if ($value !== '') {
            return $value;
        }

        if (($variationRow['type'] ?? '') !== 'size_table' || empty($optionRow['size_table_id'])) {
            return null;
        }

        $table = SizeTable::find((int) $optionRow['size_table_id']);
        if (! $table) {
            return null;
        }

        $label = trim((string) ($table->title ?: $table->name ?: ''));

        return $label !== '' ? $label : (string) $table->slug;
    }

    /**
     * Bağlı olduğu varyasyon seçenekleri (ürün varyasyonları + özelleştirme adımı).
     *
     * @return array<string, string>
     */
    public static function dependsOnVariationOptions(Get $get): array
    {
        $currentName = trim((string) ($get('name') ?? ''));
        $productId = static::resolveProductIdFromGet($get);
        $cacheKey = ($productId ?? 0).'|'.md5($currentName.'|'.(int) ($get('customization_enabled') ?? 1));

        if (isset(static::$productVariationNamesCache[$cacheKey])) {
            return static::$productVariationNamesCache[$cacheKey];
        }

        $options = [];

        if ($productId) {
            $record = Product::query()->with(['variations' => fn ($q) => $q->orderBy('sort_order')])->find($productId);
            if ($record) {
                foreach ($record->variations as $variation) {
                    $name = trim((string) $variation->name);
                    if ($name !== '' && $name !== $currentName) {
                        $options[$name] = $name;
                    }
                }
            }
        }

        if ($options === []) {
            $variations = $get('variations');
            if (is_array($variations)) {
                foreach (collect($variations)->pluck('name')->filter()->unique() as $n) {
                    $name = trim((string) $n);
                    if ($name !== '' && $name !== $currentName) {
                        $options[$name] = $name;
                    }
                }
            }
        }

        $customizationEnabled = (bool) ($get('customization_enabled') ?? true);
        if (! $customizationEnabled && $productId) {
            $customizationEnabled = (bool) Product::query()->whereKey($productId)->value('customization_enabled');
        }
        if ($customizationEnabled) {
            $options[ProductVariationFlowSteps::CUSTOMIZATION_DEPENDS_ON] = ProductVariationFlowSteps::customizationDependsOnLabel();
        }

        static::$productVariationNamesCache[$cacheKey] = $options;

        return $options;
    }

    /**
     * Bağlı olduğu varyasyonun seçenek listesi (admin çoklu seçim alanları için).
     *
     * @return array<int|string, string>
     */
    public static function resolveParentVariationOptionChoices(Get $get, string $dependsOnName, string $pathPrefix = ''): array
    {
        $dependsOnName = trim($dependsOnName);
        if ($dependsOnName === '') {
            return [];
        }

        if (ProductVariationFlowSteps::isCustomizationDependency($dependsOnName)) {
            return static::cachedCustomizationRowOptions(static::resolveProductIdFromGet($get, $pathPrefix));
        }

        $productId = static::resolveProductIdFromGet($get, $pathPrefix);
        $cacheKey = ($productId ?? 0).'|'.$dependsOnName.'|'.$pathPrefix;

        if (isset(static::$parentOptionChoicesCache[$cacheKey])) {
            return static::$parentOptionChoicesCache[$cacheKey];
        }

        $choices = static::resolveParentVariationOptionChoicesUncached($get, $dependsOnName, $pathPrefix, $productId);
        static::$parentOptionChoicesCache[$cacheKey] = $choices;

        return $choices;
    }

    /**
     * @return array<int|string, string>
     */
    protected static function resolveParentVariationOptionChoicesUncached(Get $get, string $dependsOnName, string $pathPrefix, ?int $productId): array
    {
        if ($productId) {
            $parentVar = ProductVariation::query()
                ->where('product_id', $productId)
                ->where('name', $dependsOnName)
                ->with(['options' => fn ($q) => $q->orderBy('sort_order')->orderBy('id')])
                ->first();

            if ($parentVar) {
                return $parentVar->options
                    ->mapWithKeys(fn (ProductVariationOption $option): array => [
                        $option->getKey() => (string) $option->option_value,
                    ])
                    ->all();
            }
        }

        $variations = $get($pathPrefix.'variations') ?? $get('variations') ?? [];
        if (! is_array($variations)) {
            return [];
        }

        foreach ($variations as $variation) {
            if (! is_array($variation)) {
                continue;
            }
            if (trim((string) ($variation['name'] ?? '')) !== $dependsOnName) {
                continue;
            }

            $out = [];
            foreach ($variation['options'] ?? [] as $option) {
                if (! is_array($option)) {
                    continue;
                }
                $id = $option['id'] ?? null;
                if (! $id) {
                    continue;
                }
                $label = trim((string) ($option['option_value'] ?? ''));
                $out[(int) $id] = $label !== '' ? $label : ('Seçenek #'.$id);
            }

            return $out;
        }

        return [];
    }

    /** @return array<int, string> */
    protected static function cachedSizeTableOptions(): array
    {
        if (static::$sizeTableOptionsCache !== null) {
            return static::$sizeTableOptionsCache;
        }

        static::$sizeTableOptionsCache = SizeTable::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->mapWithKeys(function (SizeTable $table): array {
                $label = trim((string) ($table->title ?: $table->name ?: ''));
                if ($label === '') {
                    $label = $table->slug;
                }

                return [$table->getKey() => $label.' ('.$table->slug.')'];
            })
            ->all();

        return static::$sizeTableOptionsCache;
    }

    /** @return array<int, string> */
    protected static function searchSizeTableOptions(string $search): array
    {
        $query = SizeTable::query()->orderBy('sort_order')->orderBy('id')->limit(50);
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%'.$search.'%')
                    ->orWhere('name', 'like', '%'.$search.'%')
                    ->orWhere('slug', 'like', '%'.$search.'%');
            });
        }

        return $query->get()->mapWithKeys(function (SizeTable $table): array {
            $label = trim((string) ($table->title ?: $table->name ?: ''));
            if ($label === '') {
                $label = $table->slug;
            }

            return [$table->getKey() => $label.' ('.$table->slug.')'];
        })->all();
    }

    protected static function sizeTableOptionLabel(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $id = (int) $value;
        $options = static::cachedSizeTableOptions();
        if (isset($options[$id])) {
            return $options[$id];
        }

        $table = SizeTable::query()->find($id);
        if (! $table) {
            return null;
        }
        $label = trim((string) ($table->title ?: $table->name ?: ''));
        if ($label === '') {
            $label = $table->slug;
        }

        return $label.' ('.$table->slug.')';
    }

    /** @return array<int, string> */
    protected static function searchInterfaceColorOptions(string $search): array
    {
        return InterfaceColorVariation::query()
            ->where('is_active', true)
            ->whereNotNull('image_path')
            ->where('image_path', '!=', '')
            ->when($search !== '', fn ($q) => $q->where('name', 'like', '%'.$search.'%'))
            ->with('fabricTypeVariation')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->limit(50)
            ->get()
            ->mapWithKeys(function (InterfaceColorVariation $preset): array {
                $label = static::formatInterfaceColorLabel($preset);
                static::$interfaceColorLabelCache[(int) $preset->getKey()] = $label;

                return [$preset->getKey() => $label];
            })
            ->all();
    }

    protected static function interfaceColorOptionLabel(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        $id = (int) $value;
        if (isset(static::$interfaceColorLabelCache[$id])) {
            return static::$interfaceColorLabelCache[$id];
        }
        $preset = InterfaceColorVariation::query()->with('fabricTypeVariation')->find($id);

        return $preset ? static::formatInterfaceColorLabel($preset) : null;
    }

    protected static function formatInterfaceColorLabel(InterfaceColorVariation $preset): string
    {
        $suffix = '#'.$preset->id;
        $base = $preset->name ? $preset->name.' · '.$suffix : $suffix;
        $g = $preset->fabricTypeVariation;
        $group = $g ? trim((string) ($g->name ?? '')) : '';

        return $group !== '' ? '['.$group.'] '.$base : $base;
    }

    /** @return array<int, string> */
    protected static function searchInterfaceFabricOptions(string $search): array
    {
        return InterfaceFabricTypeVariation::query()
            ->where('is_active', true)
            ->whereNotNull('image_path')
            ->where('image_path', '!=', '')
            ->when($search !== '', fn ($q) => $q->where('name', 'like', '%'.$search.'%'))
            ->orderBy('sort_order')
            ->orderBy('id')
            ->limit(50)
            ->get()
            ->mapWithKeys(function (InterfaceFabricTypeVariation $preset): array {
                $label = static::formatInterfaceFabricLabel($preset);
                static::$interfaceFabricLabelCache[(int) $preset->getKey()] = $label;

                return [$preset->getKey() => $label];
            })
            ->all();
    }

    protected static function interfaceFabricOptionLabel(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        $id = (int) $value;
        if (isset(static::$interfaceFabricLabelCache[$id])) {
            return static::$interfaceFabricLabelCache[$id];
        }
        $preset = InterfaceFabricTypeVariation::query()->find($id);

        return $preset ? static::formatInterfaceFabricLabel($preset) : null;
    }

    protected static function formatInterfaceFabricLabel(InterfaceFabricTypeVariation $preset): string
    {
        $name = trim((string) ($preset->name ?? ''));

        return ($name !== '' ? $name : 'Kumaş #'.$preset->id)
            .' · ×'.number_format((float) ($preset->price_multiplier ?? 1), 2, ',', '.')
            .' · #'.$preset->id;
    }

    /** @return array<int, string> */
    protected static function searchInterfaceLabelTypeOptions(string $search): array
    {
        return InterfaceLabelTypeVariation::query()
            ->where('is_active', true)
            ->when($search !== '', fn ($q) => $q->where('name', 'like', '%'.$search.'%'))
            ->orderBy('sort_order')
            ->orderBy('id')
            ->limit(50)
            ->get()
            ->mapWithKeys(function (InterfaceLabelTypeVariation $preset): array {
                $label = static::formatInterfaceLabelTypeLabel($preset);
                static::$interfaceLabelTypeLabelCache[(int) $preset->getKey()] = $label;

                return [$preset->getKey() => $label];
            })
            ->all();
    }

    protected static function interfaceLabelTypeOptionLabel(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        $id = (int) $value;
        if (isset(static::$interfaceLabelTypeLabelCache[$id])) {
            return static::$interfaceLabelTypeLabelCache[$id];
        }
        $preset = InterfaceLabelTypeVariation::query()->find($id);

        return $preset ? static::formatInterfaceLabelTypeLabel($preset) : null;
    }

    protected static function formatInterfaceLabelTypeLabel(InterfaceLabelTypeVariation $preset): string
    {
        $parts = [$preset->name];
        if ($preset->is_custom_print) {
            $parts[] = 'Özel baskı';
        }
        $positions = array_filter([
            $preset->position_front ? 'Ön' : null,
            $preset->position_back ? 'Arka' : null,
        ]);
        if ($positions !== []) {
            $parts[] = implode('/', $positions);
        }

        return implode(' · ', $parts).' · #'.$preset->id;
    }

    /** @return array<int, string> */
    protected static function searchInterfaceCertificateOptions(string $search): array
    {
        return InterfaceCertificateVariation::query()
            ->where('is_active', true)
            ->when($search !== '', fn ($q) => $q->where('name', 'like', '%'.$search.'%'))
            ->orderBy('sort_order')
            ->orderBy('id')
            ->limit(50)
            ->get()
            ->mapWithKeys(function (InterfaceCertificateVariation $preset): array {
                $label = static::formatInterfaceCertificateLabel($preset);
                static::$interfaceCertificateLabelCache[(int) $preset->getKey()] = $label;

                return [$preset->getKey() => $label];
            })
            ->all();
    }

    protected static function interfaceCertificateOptionLabel(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        $id = (int) $value;
        if (isset(static::$interfaceCertificateLabelCache[$id])) {
            return static::$interfaceCertificateLabelCache[$id];
        }
        $preset = InterfaceCertificateVariation::query()->find($id);

        return $preset ? static::formatInterfaceCertificateLabel($preset) : null;
    }

    protected static function formatInterfaceCertificateLabel(InterfaceCertificateVariation $preset): string
    {
        return $preset->name
            .' · ×'.number_format((float) $preset->price_multiplier, 2, ',', '.')
            .' · #'.$preset->id;
    }

    /** @return array<int, string> */
    protected static function searchInterfaceMoldModelOptions(string $search): array
    {
        return InterfaceMoldModelVariation::query()
            ->where('is_active', true)
            ->when($search !== '', fn ($q) => $q->where('name', 'like', '%'.$search.'%'))
            ->orderBy('sort_order')
            ->orderBy('id')
            ->limit(50)
            ->get()
            ->mapWithKeys(function (InterfaceMoldModelVariation $preset): array {
                $label = static::formatInterfaceMoldModelLabel($preset);
                static::$interfaceMoldModelLabelCache[(int) $preset->getKey()] = $label;

                return [$preset->getKey() => $label];
            })
            ->all();
    }

    protected static function interfaceMoldModelOptionLabel(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        $id = (int) $value;
        if (isset(static::$interfaceMoldModelLabelCache[$id])) {
            return static::$interfaceMoldModelLabelCache[$id];
        }
        $preset = InterfaceMoldModelVariation::query()->find($id);

        return $preset ? static::formatInterfaceMoldModelLabel($preset) : null;
    }

    protected static function formatInterfaceMoldModelLabel(InterfaceMoldModelVariation $preset): string
    {
        return $preset->name
            .' · ×'.number_format((float) $preset->price_multiplier, 2, ',', '.')
            .' · #'.$preset->id;
    }

    /** @return array<int, string> */
    protected static function searchInterfaceDeliveryMethodOptions(string $search): array
    {
        return InterfaceDeliveryMethodVariation::query()
            ->where('is_active', true)
            ->when($search !== '', fn ($q) => $q->where('name', 'like', '%'.$search.'%'))
            ->orderBy('sort_order')
            ->orderBy('id')
            ->limit(50)
            ->get()
            ->mapWithKeys(function (InterfaceDeliveryMethodVariation $preset): array {
                $label = static::formatInterfaceDeliveryMethodLabel($preset);
                static::$interfaceDeliveryMethodLabelCache[(int) $preset->getKey()] = $label;

                return [$preset->getKey() => $label];
            })
            ->all();
    }

    protected static function interfaceDeliveryMethodOptionLabel(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        $id = (int) $value;
        if (isset(static::$interfaceDeliveryMethodLabelCache[$id])) {
            return static::$interfaceDeliveryMethodLabelCache[$id];
        }
        $preset = InterfaceDeliveryMethodVariation::query()->find($id);

        return $preset ? static::formatInterfaceDeliveryMethodLabel($preset) : null;
    }

    protected static function formatInterfaceDeliveryMethodLabel(InterfaceDeliveryMethodVariation $preset): string
    {
        return $preset->name
            .' · ×'.number_format((float) $preset->price_multiplier, 2, ',', '.')
            .' · #'.$preset->id;
    }

    /** @return array<int, string> */
    protected static function searchInterfacePackagingOptions(string $search): array
    {
        return InterfacePackagingPreferenceVariation::query()
            ->where('is_active', true)
            ->when($search !== '', fn ($q) => $q->where('name', 'like', '%'.$search.'%'))
            ->orderBy('sort_order')
            ->orderBy('id')
            ->limit(50)
            ->get()
            ->mapWithKeys(function (InterfacePackagingPreferenceVariation $preset): array {
                $label = $preset->name
                    .($preset->requires_material ? ' · malzeme seçimi' : '')
                    .' · #'.$preset->id;
                static::$interfacePackagingLabelCache[(int) $preset->getKey()] = $label;

                return [$preset->getKey() => $label];
            })
            ->all();
    }

    protected static function interfacePackagingOptionLabel(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        $id = (int) $value;
        if (isset(static::$interfacePackagingLabelCache[$id])) {
            return static::$interfacePackagingLabelCache[$id];
        }
        $preset = InterfacePackagingPreferenceVariation::query()->find($id);

        return $preset ? ($preset->name.($preset->requires_material ? ' · malzeme seçimi' : '').' · #'.$preset->id) : null;
    }

    /** @return array<int, string> */
    protected static function cachedCustomizationRowOptions(?int $productId = null): array
    {
        $cacheKey = $productId ?? 0;
        if (isset(static::$customizationRowOptionsCache[$cacheKey])) {
            return static::$customizationRowOptionsCache[$cacheKey];
        }

        $query = ProductCustomizationRow::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id');

        if ($productId !== null && ProductCustomizationRow::productPivotTableExists()) {
            $query->visibleForProduct($productId);
        }

        static::$customizationRowOptionsCache[$cacheKey] = $query
            ->get()
            ->mapWithKeys(fn (ProductCustomizationRow $row): array => [
                $row->getKey() => (string) $row->position_name,
            ])
            ->all();

        return static::$customizationRowOptionsCache[$cacheKey];
    }

    /** Ambalaj ve beden tablosu varyasyonlarında mağazada yalnızca tek seçim desteklenir. */
    protected static function variationTypeRequiresSingleSelectOnly(?string $type): bool
    {
        return in_array((string) $type, ['size_table', 'packaging_type'], true);
    }

    /** Select, görsel, etiket vb. tiplerde seçim modu admin tarafından ayarlanabilir. */
    protected static function variationTypeAllowsConfigurableSelectionMode(?string $type): bool
    {
        return in_array((string) $type, ['select', 'color', 'fabric', 'image', 'label_type', 'certificate_type', 'mold_model_type', 'delivery_type'], true);
    }

    protected static function variationTypeShowsSelectionModeSection(?string $type): bool
    {
        return static::variationTypeAllowsConfigurableSelectionMode($type)
            || static::variationTypeRequiresSingleSelectOnly($type);
    }

    protected static function variationSelectionModeLockedHint(?string $type): string
    {
        return match ((string) $type) {
            'packaging_type' => 'Ambalaj türü varyasyonlarında mağazada yalnızca tek seçenek seçilebilir; çoklu seçim kapalıdır.',
            'size_table' => 'Beden tablosu varyasyonlarında mağazada yalnızca tek seçenek seçilebilir; çoklu seçim kapalıdır.',
            default => 'Bu varyasyon tipinde yalnızca tek seçim desteklenir.',
        };
    }

    protected static function resolveProductIdFromGet(Get $get, string $pathPrefix = ''): ?int
    {
        $component = Livewire::current();
        if ($component && method_exists($component, 'getRecord')) {
            $record = $component->getRecord();
            if ($record instanceof Product && $record->exists) {
                return (int) $record->getKey();
            }
        }

        $productId = $get($pathPrefix.'_product_id')
            ?? $get('_product_id')
            ?? $get($pathPrefix.'id')
            ?? $get('id');

        return $productId ? (int) $productId : null;
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
