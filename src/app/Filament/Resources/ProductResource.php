<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Category;
use App\Models\Currency;
use App\Models\CustomerGroup;
use App\Models\InterfaceColorVariation;
use App\Models\InterfaceFabricTypeVariation;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\ProductVariationOption;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
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
                                    ->description('Renk, beden vb. varyasyonlar tanımlayın. Her varyasyonun altına birden fazla seçenek ekleyebilir ve seçeneklere görsel yükleyebilirsiniz. Seçim modunda tek seçim veya çoklu seçim seçebilirsiniz.')
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
                                                        'fabric' => 'Kumaş Türü',
                                                        'image' => 'Görsel',
                                                    ])
                                                    ->default('select')
                                                    ->required()
                                                    ->live()
                                                    ->afterStateUpdated(function ($state, Set $set): void {
                                                        if ($state === 'fabric') {
                                                            $rows = static::fabricVariationOptionsFromInterfacePresets();
                                                            $set('options', $rows);
                                                            if ($rows === []) {
                                                                Notification::make()
                                                                    ->warning()
                                                                    ->title('Kumaş türü kaydı bulunamadı')
                                                                    ->body('Önce Arayüz → Kumaş türü varyasyonları bölümünden kayıt ekleyin.')
                                                                    ->send();
                                                            }

                                                            return;
                                                        }
                                                        if ($state === 'color') {
                                                            $rows = static::colorVariationOptionsFromInterfacePresets();
                                                            $set('options', $rows);
                                                            if ($rows === []) {
                                                                Notification::make()
                                                                    ->warning()
                                                                    ->title('Renk kaydı bulunamadı')
                                                                    ->body('Önce Arayüz → Renk varyasyonları bölümünden görseli olan aktif kayıtlar ekleyin.')
                                                                    ->send();
                                                            }
                                                        }
                                                    })
                                                    ->helperText(fn (Get $get): ?string => match ($get('type')) {
                                                        'fabric' => 'Seçenekler, Kumaş türü varyasyonları kayıtlarından otomatik doldurulur (mevcut seçenek satırlarının yerine geçer).',
                                                        'color' => 'Seçenekler, Renk varyasyonları kayıtlarından otomatik doldurulur; kumaş türü grubuna göre sıralanır (mevcut seçenek satırlarının yerine geçer).',
                                                        default => null,
                                                    })
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
                                                Forms\Components\Toggle::make('replace_main_gallery_image')
                                                    ->label('Seçilen seçeneğin görseli sol ürün görselinde gösterilsin')
                                                    ->helperText('Açıksa, müşteri bu varyasyonda seçim yaptığında seçeneğin görseli (varsa) mağazada sol taraftaki ana ürün galerisinin ilk görselinin yerine geçer. Birden fazla varyasyonda işaretliyse küçük sıra numarası önceliklidir.')
                                                    ->default(false)
                                                    ->columnSpanFull(),
                                                Forms\Components\Radio::make('allows_multiple')
                                                    ->label('Seçim modu')
                                                    ->options([
                                                        0 => 'Tek seçim — müşteri yalnızca bir seçenek seçer; seçimden sonra otomatik olarak sonraki adıma geçilir.',
                                                        1 => 'Çoklu seçim — birden fazla seçenek seçilebilir; seçtikten sonra "Devam et" ile sonraki adıma geçilir.',
                                                    ])
                                                    ->default(0)
                                                    ->columnSpanFull(),
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
                                                        Forms\Components\Select::make('interface_color_variation_id')
                                                            ->label('Kayıtlı renk görseli (Arayüz)')
                                                            ->visible(fn (Get $get): bool => ($get('../../type') ?? '') === 'color')
                                                            ->options(function (): array {
                                                                return InterfaceColorVariation::query()
                                                                    ->where('is_active', true)
                                                                    ->whereNotNull('image_path')
                                                                    ->where('image_path', '!=', '')
                                                                    ->with('fabricTypeVariation')
                                                                    ->orderBy('sort_order')
                                                                    ->orderBy('id')
                                                                    ->get()
                                                                    ->mapWithKeys(function (InterfaceColorVariation $preset): array {
                                                                        $suffix = '#'.$preset->id;
                                                                        $base = $preset->name ? $preset->name.' · '.$suffix : $suffix;
                                                                        $g = $preset->fabricTypeVariation;
                                                                        $group = $g ? trim((string) ($g->name ?? '')) : '';
                                                                        $label = $group !== '' ? '['.$group.'] '.$base : $base;

                                                                        return [$preset->id => $label];
                                                                    })
                                                                    ->all();
                                                            })
                                                            ->searchable()
                                                            ->preload()
                                                            ->nullable()
                                                            ->live()
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
                                                            ->helperText('Arayüz Yönetimi → Renk varyasyonlarından tanımlı görsel seçilir ve aşağıdaki görsel alanı güncellenir. Özel yükleme kullanırsanız preset sıfırlanır.')
                                                            ->columnSpanFull(),
                                                        Forms\Components\Select::make('interface_fabric_type_variation_id')
                                                            ->label('Kayıtlı kumaş görseli (Arayüz)')
                                                            ->visible(fn (Get $get): bool => ($get('../../type') ?? '') === 'fabric')
                                                            ->options(function (): array {
                                                                return InterfaceFabricTypeVariation::query()
                                                                    ->where('is_active', true)
                                                                    ->whereNotNull('image_path')
                                                                    ->where('image_path', '!=', '')
                                                                    ->orderBy('sort_order')
                                                                    ->orderBy('id')
                                                                    ->get()
                                                                    ->mapWithKeys(function (InterfaceFabricTypeVariation $preset): array {
                                                                        $suffix = '#'.$preset->id;

                                                                        return [$preset->id => ($preset->name ? $preset->name.' · '.$suffix : $suffix)];
                                                                    })
                                                                    ->all();
                                                            })
                                                            ->searchable()
                                                            ->preload()
                                                            ->nullable()
                                                            ->live()
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
                                                                if (is_string($preset->image_path) && $preset->image_path !== '') {
                                                                    $set('option_image', [$preset->image_path]);
                                                                }
                                                            })
                                                            ->helperText('Arayüz Yönetimi → Kumaş türü varyasyonlarından görsel seçilir; özel yüklemede preset sıfırlanır.')
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
                                                            ->image()
                                                            ->disk('public')
                                                            ->directory('variation_options')
                                                            ->visibility('public')
                                                            ->imagePreviewHeight(120)
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
                                                                    ->mapWithKeys(fn ($o) => [$o->id => ($o->variation ? $o->variation->name.' — ' : '').$o->option_value])
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
                                                Forms\Components\Section::make('Tek başına seçim seçeneği')
                                                    ->description('Çoklu seçim modunda bu metinle eşleşen seçenek seçilince diğer işaretler kalkar ve otomatik sonraki adıma geçilir.')
                                                    ->schema([
                                                        Forms\Components\TextInput::make('solo_option_value')
                                                            ->label('Seçenek metni')
                                                            ->placeholder('Örn: İstemiyorum')
                                                            ->maxLength(255)
                                                            ->nullable()
                                                            ->helperText('Yukarıdaki seçeneklerden birinin değeriyle birebir aynı yazın. Boş bırakırsanız solo davranış olmaz.')
                                                            ->columnSpanFull(),
                                                    ])
                                                    ->columns(1)
                                                    ->columnSpanFull(),
                                            ])
                                            ->columns(2)
                                            ->columnSpanFull(),
                                        Forms\Components\Section::make('Mağaza: Beden tablosu')
                                            ->description('Ürün sayfasında "Seçenekleri belirleyin" alanındaki beden tablolarının hangi koşulda görüneceğini buradan yönetirsiniz.')
                                            ->schema([
                                                Forms\Components\Select::make('size_table_trigger_variation')
                                                    ->label('Beden tabloları için ek önkoşul varyasyon')
                                                    ->placeholder('Yok — yalnızca tüm seçimler yeterli')
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
                                                        $current = trim((string) ($get('size_table_trigger_variation') ?? ''));
                                                        if ($current !== '' && ! $names->contains($current)) {
                                                            $names->push($current);
                                                        }

                                                        return $names->unique()->filter()->sort()->values()
                                                            ->mapWithKeys(fn (string $n): array => [$n => $n])
                                                            ->all();
                                                    })
                                                    ->searchable()
                                                    ->nullable()
                                                    ->helperText('Boş bırakın: Müşteri tüm varyasyonları seçtikten sonra beden tablosu adımı açılır; hangi tablonun (Erkek/Kadın vb.) görüneceği Arayüz → Beden tabloları kaydındaki tetikleyici varyasyon/seçenek ile belirlenir. Bir varyasyon adı seçerseniz: mağazada bu varyasyonda da seçim yapılmadan beden tablo içerikleri gösterilmez (ör. önce kumaş seçilsin). Üstteki varyasyon adlarıyla birebir eşleşmelidir.'),
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
    public static function fabricVariationOptionsFromInterfacePresets(): array
    {
        return InterfaceFabricTypeVariation::query()
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
                    'interface_color_variation_id' => null,
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
            ->map(function (InterfaceColorVariation $preset, int $index): array {
                $label = trim((string) ($preset->name ?? ''));
                if ($label === '') {
                    $label = 'Renk #'.$preset->getKey();
                }

                return [
                    'option_value' => $label,
                    'interface_color_variation_id' => $preset->getKey(),
                    'interface_fabric_type_variation_id' => null,
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
     * Ürünün "Renk" varyasyonu seçeneklerini Arayüz → Renk varyasyonları kayıtlarıyla yeniler.
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
            $image = $row['option_image'] ?? null;
            if (is_array($image)) {
                $image = $image[0] ?? null;
            }

            ProductVariationOption::query()->create([
                'product_variation_id' => $variation->getKey(),
                'option_value' => $row['option_value'],
                'interface_color_variation_id' => $row['interface_color_variation_id'],
                'interface_fabric_type_variation_id' => null,
                'option_image' => is_string($image) && $image !== '' ? $image : null,
                'option_color' => null,
                'sort_order' => (int) ($row['sort_order'] ?? 0),
                'price_delta' => (float) ($row['price_delta'] ?? 0),
                'stock_quantity' => $row['stock_quantity'] ?? null,
                'parent_option_id' => $row['parent_option_id'] ?? null,
                'parent_option_ids' => $row['parent_option_ids'] ?? null,
            ]);
            $created++;
        }

        return $created;
    }

    /**
     * Varyasyon seçeneklerinde yüklenen görsel ve Arayüz renk / kumaş preset’lerini kayıt öncesi normalize eder.
     */
    public static function finalizeVariationOptionsInProductFormData(array $data): array
    {
        if (empty($data['variations']) || ! is_array($data['variations'])) {
            return $data;
        }

        foreach ($data['variations'] as &$variation) {
            if (empty($variation['options']) || ! is_array($variation['options'])) {
                continue;
            }

            $variationType = (string) ($variation['type'] ?? '');

            foreach ($variation['options'] as &$opt) {
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
        $formData = self::finalizeVariationOptionsInProductFormData($formData);

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

                $option->update([
                    'option_image' => $path,
                    'interface_color_variation_id' => $optionRow['interface_color_variation_id'] ?? null,
                    'interface_fabric_type_variation_id' => $optionRow['interface_fabric_type_variation_id'] ?? null,
                ]);
            }
        }
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
