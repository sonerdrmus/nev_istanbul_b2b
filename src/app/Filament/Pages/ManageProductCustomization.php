<?php

namespace App\Filament\Pages;

use App\Filament\Forms\Components\ProductMultiSelect;
use App\Models\ProductCustomizationPrintTechnique;
use App\Models\ProductCustomizationRow;
use App\Models\ProductCustomizationSetting;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use App\Support\PrintTechniqueSlugResolver;
use Illuminate\Support\Str;

class ManageProductCustomization extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';

    protected static ?string $navigationLabel = 'Ürün Özelleştirme';

    protected static ?string $title = 'Ürün Özelleştirme';

    protected static ?string $navigationGroup = 'Varyasyon yönetimi';

    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.pages.manage-product-customization';

    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    public static function getSlug(): string
    {
        return 'product-customization';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('sizeMultipliers')
                ->label('Çarpan Yönetimi (varsayılan şablon)')
                ->icon('heroicon-o-calculator')
                ->url(fn (): string => ManageSizeDimensionMultipliers::getUrl())
                ->color('gray'),
        ];
    }

    public function mount(): void
    {
        $settings = ProductCustomizationSetting::instance();

        $this->form->fill([
            'max_color_count' => $settings->max_color_count,
            'default_print_technique_slug' => $settings->default_print_technique_slug,
            'print_techniques' => ProductCustomizationPrintTechnique::query()
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get()
                ->map(fn (ProductCustomizationPrintTechnique $t): array => [
                    'id' => $t->id,
                    'name' => $t->name,
                    'slug' => $t->slug,
                    'sort_order' => $t->sort_order,
                    'is_active' => $t->is_active,
                ])
                ->values()
                ->all(),
            'rows' => ProductCustomizationRow::query()
                ->with('products')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get()
                ->map(fn (ProductCustomizationRow $r): array => [
                    'id' => $r->id,
                    'position_name' => $r->position_name,
                    'position_image' => filled($r->position_image) ? [$r->position_image] : null,
                    'default_width' => $r->default_width,
                    'default_height' => $r->default_height,
                    'default_color_count' => $r->default_color_count,
                    'default_print_technique_slug' => $r->default_print_technique_slug,
                    'product_ids' => $r->products->pluck('id')->map(fn ($id): int => (int) $id)->all(),
                    'sort_order' => $r->sort_order,
                    'is_active' => $r->is_active,
                ])
                ->values()
                ->all(),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Genel ayarlar')
                    ->description('Mağaza ürün sayfasındaki özelleştirme tablosunun genel kuralları.')
                    ->schema([
                        Forms\Components\TextInput::make('max_color_count')
                            ->label('Maksimum renk sayısı')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(20)
                            ->required()
                            ->default(7)
                            ->helperText('Renk sayısı açılır listesinde 1 ile bu değer arasında seçenek oluşturulur.'),
                        Forms\Components\Select::make('default_print_technique_slug')
                            ->label('Varsayılan baskı tekniği')
                            ->options(fn (): array => self::printTechniqueOptions())
                            ->searchable()
                            ->nullable(),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Baskı teknikleri')
                    ->description('Tablodaki "Baskı Tekniği" sütununda listelenecek seçenekler.')
                    ->schema([
                        Forms\Components\Repeater::make('print_techniques')
                            ->label('')
                            ->view('filament.forms.components.form-table-repeater')
                            ->viewData([
                                'tableHeaders' => [
                                    'Ad',
                                    'Kod (slug)',
                                    ['label' => 'Aktif', 'align' => 'center'],
                                ],
                                'emptyMessage' => 'Henüz baskı tekniği yok. Aşağıdan ekleyin.',
                                'tableMinWidth' => '36rem',
                            ])
                            ->schema([
                                Forms\Components\Hidden::make('id'),
                                Forms\Components\TextInput::make('name')
                                    ->label('Ad')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->hiddenLabel()
                                    ->afterStateUpdated(function (Forms\Set $set, ?string $state, Get $get): void {
                                        if (filled($get('slug'))) {
                                            return;
                                        }
                                        $set('slug', Str::slug($state ?? ''));
                                    }),
                                Forms\Components\TextInput::make('slug')
                                    ->label('Kod (slug)')
                                    ->required()
                                    ->maxLength(64)
                                    ->alphaDash()
                                    ->hiddenLabel(),
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Aktif')
                                    ->default(true)
                                    ->inline(false)
                                    ->hiddenLabel(),
                            ])
                            ->defaultItems(0)
                            ->addActionLabel('Baskı tekniği ekle')
                            ->reorderableWithDragAndDrop()
                            ->collapsible(false)
                            ->cloneable(false),
                    ]),
                Forms\Components\Section::make('Tablo satırları')
                    ->description('Baskı konumları ve hangi ürünlerde görünecekleri. Ürün seçimi satırın içinde aramalıdır; tabloyu şişirmez.')
                    ->schema([
                        Forms\Components\Repeater::make('rows')
                            ->label('')
                            ->schema([
                                Forms\Components\Hidden::make('id'),
                                Forms\Components\Grid::make(12)
                                    ->schema([
                                        Forms\Components\TextInput::make('position_name')
                                            ->label('Konum')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpan(4),
                                        Forms\Components\TextInput::make('default_width')
                                            ->label('En (cm)')
                                            ->numeric()
                                            ->minValue(0)
                                            ->step(0.01)
                                            ->columnSpan(2),
                                        Forms\Components\TextInput::make('default_height')
                                            ->label('Boy (cm)')
                                            ->numeric()
                                            ->minValue(0)
                                            ->step(0.01)
                                            ->columnSpan(2),
                                        Forms\Components\TextInput::make('default_color_count')
                                            ->label('Renk sayısı')
                                            ->numeric()
                                            ->minValue(1)
                                            ->maxValue(20)
                                            ->default(3)
                                            ->columnSpan(2),
                                        Forms\Components\Select::make('default_print_technique_slug')
                                            ->label('Baskı tekniği')
                                            ->options(fn (): array => self::printTechniqueOptions())
                                            ->searchable()
                                            ->nullable()
                                            ->columnSpan(2),
                                        Forms\Components\FileUpload::make('position_image')
                                            ->label('Konum görseli')
                                            ->directory('product_customization_positions')
                                            ->disk('public')
                                            ->visibility('public')
                                            ->image()
                                            ->imageEditor()
                                            ->imagePreviewHeight('6rem')
                                            ->maxSize(2048)
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                            ->nullable()
                                            ->helperText('PNG/JPG/WEBP, en fazla 2 MB.')
                                            ->columnSpan(6),
                                        Forms\Components\Toggle::make('is_active')
                                            ->label('Aktif')
                                            ->default(true)
                                            ->inline(false)
                                            ->columnSpan(6),
                                        ProductMultiSelect::make('product_ids')
                                            ->label('Bu konumun görüneceği ürünler')
                                            ->helperText('Ürün adı yazarak arayın. Yalnızca seçili ürünlerin sayfasında bu konum listelenir.')
                                            ->columnSpanFull(),
                                    ]),
                            ])
                            ->itemLabel(function (?array $state): string {
                                $name = trim((string) ($state['position_name'] ?? ''));
                                $label = $name !== '' ? $name : 'Yeni konum';
                                $count = is_array($state['product_ids'] ?? null) ? count($state['product_ids']) : 0;
                                if ($count > 0) {
                                    $label .= ' · '.$count.' ürün';
                                } else {
                                    $label .= ' · ürün atanmamış';
                                }

                                return $label;
                            })
                            ->collapsed()
                            ->collapsible()
                            ->cloneable(false)
                            ->reorderableWithDragAndDrop()
                            ->defaultItems(0)
                            ->addActionLabel('Konum satırı ekle')
                            ->extraAttributes(['class' => 'ni-customization-rows-repeater']),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Kaydet')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $settings = ProductCustomizationSetting::instance();
        $settings->update([
            'max_color_count' => max(1, min(20, (int) ($data['max_color_count'] ?? 7))),
            'default_print_technique_slug' => filled($data['default_print_technique_slug'] ?? null)
                ? PrintTechniqueSlugResolver::canonical((string) $data['default_print_technique_slug'])
                : null,
        ]);

        $keptTechniqueIds = [];
        $sort = 0;
        $seenSlugs = [];
        foreach ($data['print_techniques'] ?? [] as $row) {
            $rawSlug = Str::slug((string) ($row['slug'] ?? $row['name'] ?? ''));
            if ($rawSlug === '') {
                continue;
            }
            $slug = PrintTechniqueSlugResolver::canonical($rawSlug);
            if (isset($seenSlugs[$slug])) {
                Notification::make()
                    ->title('Aynı slug iki kez kullanılamaz')
                    ->body('“'.$slug.'” kodu birden fazla baskı tekniğinde tekrarlanıyor.')
                    ->danger()
                    ->send();

                return;
            }
            $seenSlugs[$slug] = true;

            $duplicateQuery = ProductCustomizationPrintTechnique::query()->where('slug', $slug);
            if (! empty($row['id'])) {
                $duplicateQuery->where('id', '!=', (int) $row['id']);
            }
            if ($duplicateQuery->exists()) {
                Notification::make()
                    ->title('Bu slug zaten kayıtlı')
                    ->body('“'.$slug.'” kodu başka bir baskı tekniğinde kullanılıyor. Farklı bir kod (slug) girin.')
                    ->danger()
                    ->send();

                return;
            }

            $attrs = [
                'name' => trim((string) ($row['name'] ?? $slug)),
                'slug' => $slug,
                'sort_order' => (int) ($row['sort_order'] ?? ($sort * 10)),
                'is_active' => (bool) ($row['is_active'] ?? true),
            ];
            if (! empty($row['id'])) {
                $model = ProductCustomizationPrintTechnique::query()->find((int) $row['id']);
                if ($model) {
                    $model->update($attrs);
                    $keptTechniqueIds[] = $model->id;
                    $sort++;

                    continue;
                }
            }
            $model = ProductCustomizationPrintTechnique::query()->create($attrs);
            $keptTechniqueIds[] = $model->id;
            $sort++;
        }
        ProductCustomizationPrintTechnique::query()
            ->when($keptTechniqueIds !== [], fn ($q) => $q->whereNotIn('id', $keptTechniqueIds))
            ->delete();

        $keptRowIds = [];
        $sort = 0;
        foreach ($data['rows'] ?? [] as $row) {
            $position = trim((string) ($row['position_name'] ?? ''));
            if ($position === '') {
                continue;
            }
            $attrs = [
                'position_name' => $position,
                'position_image' => self::normalizeUploadedPath($row['position_image'] ?? null),
                'default_width' => filled($row['default_width'] ?? null) ? $row['default_width'] : null,
                'default_height' => filled($row['default_height'] ?? null) ? $row['default_height'] : null,
                'default_color_count' => max(1, min(20, (int) ($row['default_color_count'] ?? 3))),
                'default_print_technique_slug' => filled($row['default_print_technique_slug'] ?? null)
                    ? PrintTechniqueSlugResolver::canonical((string) $row['default_print_technique_slug'])
                    : null,
                'sort_order' => (int) ($row['sort_order'] ?? ($sort * 10)),
                'is_active' => (bool) ($row['is_active'] ?? true),
            ];
            if (! empty($row['id'])) {
                $model = ProductCustomizationRow::query()->find((int) $row['id']);
                if ($model) {
                    $model->update($attrs);
                    $keptRowIds[] = $model->id;
                    if (ProductCustomizationRow::productPivotTableExists()) {
                        $model->products()->sync(
                            collect($row['product_ids'] ?? [])->map(fn ($id): int => (int) $id)->filter()->all()
                        );
                    }
                    $sort++;

                    continue;
                }
            }
            $model = ProductCustomizationRow::query()->create($attrs);
            $keptRowIds[] = $model->id;
            if (ProductCustomizationRow::productPivotTableExists()) {
                $model->products()->sync(
                    collect($row['product_ids'] ?? [])->map(fn ($id): int => (int) $id)->filter()->all()
                );
            }
            $sort++;
        }
        ProductCustomizationRow::query()
            ->when($keptRowIds !== [], fn ($q) => $q->whereNotIn('id', $keptRowIds))
            ->delete();

        Notification::make()
            ->title('Ürün özelleştirme kaydedildi')
            ->success()
            ->send();

        $this->mount();
    }

    /** @return array<string, string> */
    private static function printTechniqueOptions(): array
    {
        return ProductCustomizationPrintTechnique::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->pluck('name', 'slug')
            ->all();
    }

    private static function normalizeUploadedPath(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (is_array($value)) {
            $value = reset($value);
        }
        $path = trim((string) $value);

        return $path !== '' ? $path : null;
    }
}
