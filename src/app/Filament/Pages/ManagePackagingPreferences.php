<?php

namespace App\Filament\Pages;

use App\Models\InterfacePackagingCustomization;
use App\Models\InterfacePackagingMaterial;
use App\Models\InterfacePackagingPreferenceVariation;
use App\Models\InterfacePackagingSetting;
use App\Support\ProductVariationOptionInterfaceSync;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Str;

class ManagePackagingPreferences extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-gift';

    protected static ?string $navigationLabel = 'Ambalaj Tercih Yönetimi';

    protected static ?string $title = 'Ambalaj Tercih Yönetimi';

    protected static ?string $navigationGroup = 'Varyasyon yönetimi';

    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.pages.manage-packaging-preferences';

    public ?array $data = [];

    public static function getSlug(): string
    {
        return 'packaging-preferences';
    }

    public function mount(): void
    {
        $settings = InterfacePackagingSetting::instance();

        $this->form->fill([
            'packaging_types' => InterfacePackagingPreferenceVariation::query()
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get()
                ->map(fn (InterfacePackagingPreferenceVariation $row): array => [
                    'id' => $row->id,
                    'name' => $row->name,
                    'slug' => $row->slug,
                    'image_path' => $row->image_path,
                    'requires_material' => $row->requires_material,
                    'is_active' => $row->is_active,
                ])
                ->values()
                ->all(),
            'materials' => InterfacePackagingMaterial::query()
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get()
                ->map(fn (InterfacePackagingMaterial $row): array => [
                    'id' => $row->id,
                    'name' => $row->name,
                    'slug' => $row->slug,
                    'is_active' => $row->is_active,
                ])
                ->values()
                ->all(),
            'customizations' => InterfacePackagingCustomization::query()
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get()
                ->map(fn (InterfacePackagingCustomization $row): array => [
                    'id' => $row->id,
                    'name' => $row->name,
                    'slug' => $row->slug,
                    'extra_price' => $row->extra_price,
                    'is_default' => $row->is_default,
                    'is_active' => $row->is_active,
                ])
                ->values()
                ->all(),
            'barcode_enabled' => $settings->barcode_enabled,
            'barcode_label' => $settings->barcode_label,
            'barcode_extra_price' => $settings->barcode_extra_price,
            'barcode_description' => $settings->barcode_description,
            'barcode_image_path' => $settings->barcode_image_path,
            'customizations_enabled' => (bool) ($settings->customizations_enabled ?? true),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Ambalaj seç')
                    ->description('Mağazada müşterinin ilk seçeceği ambalaj türleri (ör. OPP Şeffaf, Kilitli Poşet).')
                    ->schema([
                        Forms\Components\Repeater::make('packaging_types')
                            ->label('')
                            ->schema([
                                Forms\Components\Hidden::make('id'),
                                Forms\Components\TextInput::make('name')
                                    ->label('Ambalaj adı')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Forms\Set $set, ?string $state, Forms\Get $get): void {
                                        if (filled($get('slug'))) {
                                            return;
                                        }
                                        $set('slug', Str::slug($state ?? ''));
                                    }),
                                Forms\Components\TextInput::make('slug')
                                    ->label('Kod (slug)')
                                    ->required()
                                    ->maxLength(64)
                                    ->alphaDash(),
                                Forms\Components\FileUpload::make('image_path')
                                    ->label('Görsel')
                                    ->directory('interface_packaging_preference_variations')
                                    ->visibility('public')
                                    ->image()
                                    ->imageEditor()
                                    ->nullable(),
                                Forms\Components\Toggle::make('requires_material')
                                    ->label('Malzeme seçimi gerekir (Kilitli poşet)')
                                    ->default(false)
                                    ->inline(false),
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Yayında')
                                    ->default(true)
                                    ->inline(false),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->addActionLabel('Ambalaj türü ekle')
                            ->reorderableWithDragAndDrop()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null),
                    ]),
                Forms\Components\Section::make('Malzeme seç (Kilitli poşet)')
                    ->description('“Malzeme seçimi gerekir” işaretli ambalaj türü seçildiğinde mağazada gösterilir.')
                    ->schema([
                        Forms\Components\Repeater::make('materials')
                            ->label('')
                            ->schema([
                                Forms\Components\Hidden::make('id'),
                                Forms\Components\TextInput::make('name')
                                    ->label('Malzeme adı')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Forms\Set $set, ?string $state, Forms\Get $get): void {
                                        if (filled($get('slug'))) {
                                            return;
                                        }
                                        $set('slug', Str::slug($state ?? ''));
                                    }),
                                Forms\Components\TextInput::make('slug')
                                    ->label('Kod (slug)')
                                    ->required()
                                    ->maxLength(64)
                                    ->alphaDash(),
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Yayında')
                                    ->default(true)
                                    ->inline(false),
                            ])
                            ->columns(3)
                            ->defaultItems(0)
                            ->addActionLabel('Malzeme ekle')
                            ->reorderableWithDragAndDrop()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null),
                    ]),
                Forms\Components\Section::make('Özelleştirme ekle')
                    ->description('Kapalıysa mağaza ürün sayfasında “Özelleştirme ekleyin” adımı hiç gösterilmez. Açıkken müşteri tek seçim yapar; ekstra ücret TL olarak birim fiyata eklenir.')
                    ->schema([
                        Forms\Components\Toggle::make('customizations_enabled')
                            ->label('Özelleştirme ekleyin adımını göster')
                            ->helperText('Pasif yapıldığında ürün sayfasında ambalaj özelleştirme seçenekleri gizlenir.')
                            ->default(true)
                            ->live(),
                        Forms\Components\Repeater::make('customizations')
                            ->label('')
                            ->visible(fn (Forms\Get $get): bool => (bool) $get('customizations_enabled'))
                            ->schema([
                                Forms\Components\Hidden::make('id'),
                                Forms\Components\TextInput::make('name')
                                    ->label('Seçenek adı')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Forms\Set $set, ?string $state, Forms\Get $get): void {
                                        if (filled($get('slug'))) {
                                            return;
                                        }
                                        $set('slug', Str::slug($state ?? ''));
                                    }),
                                Forms\Components\TextInput::make('slug')
                                    ->label('Kod (slug)')
                                    ->required()
                                    ->maxLength(64)
                                    ->alphaDash(),
                                Forms\Components\TextInput::make('extra_price')
                                    ->label('Ekstra ücret (TL)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->default(0)
                                    ->prefix('₺'),
                                Forms\Components\Toggle::make('is_default')
                                    ->label('Varsayılan (Standart)')
                                    ->default(false)
                                    ->inline(false),
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Yayında')
                                    ->default(true)
                                    ->inline(false),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->addActionLabel('Özelleştirme seçeneği ekle')
                            ->reorderableWithDragAndDrop()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null),
                    ]),
                Forms\Components\Section::make('Ekstra özellik (opsiyonel)')
                    ->description('Özellikle OPP poşet görselindeki barkod/etiket alanı talebi için.')
                    ->schema([
                        Forms\Components\Toggle::make('barcode_enabled')
                            ->label('Barkod / etiket alanı seçeneğini göster')
                            ->default(true),
                        Forms\Components\TextInput::make('barcode_label')
                            ->label('Checkbox metni')
                            ->maxLength(255)
                            ->default('Barkod / Etiket Alanı İstiyorum'),
                        Forms\Components\TextInput::make('barcode_extra_price')
                            ->label('Ekstra ücret (TL)')
                            ->numeric()
                            ->minValue(0)
                            ->step(0.01)
                            ->default(0)
                            ->prefix('₺'),
                        Forms\Components\Textarea::make('barcode_description')
                            ->label('Açıklama / ipucu')
                            ->rows(2)
                            ->maxLength(1000)
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('barcode_image_path')
                            ->label('OPP poşet referans görseli')
                            ->directory('interface_packaging_preference_variations')
                            ->visibility('public')
                            ->image()
                            ->imageEditor()
                            ->nullable()
                            ->helperText('Mağazada müşteriye gösterilecek örnek görsel (opsiyonel).')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
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

        $keptTypeIds = [];
        $sort = 0;
        foreach ($data['packaging_types'] ?? [] as $row) {
            $name = trim((string) ($row['name'] ?? ''));
            if ($name === '') {
                continue;
            }
            $slug = Str::slug((string) ($row['slug'] ?? $name));
            if ($slug === '') {
                continue;
            }
            $attrs = [
                'name' => $name,
                'slug' => $slug,
                'image_path' => $row['image_path'] ?? null,
                'requires_material' => (bool) ($row['requires_material'] ?? false),
                'sort_order' => $sort * 10,
                'is_active' => (bool) ($row['is_active'] ?? true),
            ];
            if (! empty($row['id'])) {
                $model = InterfacePackagingPreferenceVariation::query()->find((int) $row['id']);
                if ($model) {
                    $model->update($attrs);
                    $keptTypeIds[] = $model->id;
                    $sort++;

                    continue;
                }
            }
            $model = InterfacePackagingPreferenceVariation::query()->create($attrs);
            $keptTypeIds[] = $model->id;
            $sort++;
        }
        InterfacePackagingPreferenceVariation::query()
            ->when($keptTypeIds !== [], fn ($q) => $q->whereNotIn('id', $keptTypeIds))
            ->delete();

        $productSync = ProductVariationOptionInterfaceSync::syncVariationType('packaging_type');

        $keptMaterialIds = [];
        $sort = 0;
        foreach ($data['materials'] ?? [] as $row) {
            $name = trim((string) ($row['name'] ?? ''));
            if ($name === '') {
                continue;
            }
            $slug = Str::slug((string) ($row['slug'] ?? $name));
            if ($slug === '') {
                continue;
            }
            $attrs = [
                'name' => $name,
                'slug' => $slug,
                'sort_order' => $sort * 10,
                'is_active' => (bool) ($row['is_active'] ?? true),
            ];
            if (! empty($row['id'])) {
                $model = InterfacePackagingMaterial::query()->find((int) $row['id']);
                if ($model) {
                    $model->update($attrs);
                    $keptMaterialIds[] = $model->id;
                    $sort++;

                    continue;
                }
            }
            $model = InterfacePackagingMaterial::query()->create($attrs);
            $keptMaterialIds[] = $model->id;
            $sort++;
        }
        InterfacePackagingMaterial::query()
            ->when($keptMaterialIds !== [], fn ($q) => $q->whereNotIn('id', $keptMaterialIds))
            ->delete();

        $keptCustomizationIds = [];
        $sort = 0;
        $defaultCustomizationId = null;
        foreach ($data['customizations'] ?? [] as $row) {
            $name = trim((string) ($row['name'] ?? ''));
            if ($name === '') {
                continue;
            }
            $slug = Str::slug((string) ($row['slug'] ?? $name));
            if ($slug === '') {
                continue;
            }
            $isDefault = (bool) ($row['is_default'] ?? false);
            $attrs = [
                'name' => $name,
                'slug' => $slug,
                'extra_price' => max(0, (float) ($row['extra_price'] ?? 0)),
                'is_default' => $isDefault,
                'sort_order' => $sort * 10,
                'is_active' => (bool) ($row['is_active'] ?? true),
            ];
            if (! empty($row['id'])) {
                $model = InterfacePackagingCustomization::query()->find((int) $row['id']);
                if ($model) {
                    $model->update($attrs);
                    $keptCustomizationIds[] = $model->id;
                    if ($isDefault) {
                        $defaultCustomizationId = $model->id;
                    }
                    $sort++;

                    continue;
                }
            }
            $model = InterfacePackagingCustomization::query()->create($attrs);
            $keptCustomizationIds[] = $model->id;
            if ($isDefault) {
                $defaultCustomizationId = $model->id;
            }
            $sort++;
        }
        InterfacePackagingCustomization::query()
            ->when($keptCustomizationIds !== [], fn ($q) => $q->whereNotIn('id', $keptCustomizationIds))
            ->delete();

        if ($defaultCustomizationId !== null) {
            InterfacePackagingCustomization::query()
                ->where('id', '!=', $defaultCustomizationId)
                ->update(['is_default' => false]);
        } elseif ($keptCustomizationIds !== []) {
            $first = InterfacePackagingCustomization::query()->find($keptCustomizationIds[0]);
            if ($first) {
                $first->update(['is_default' => true]);
            }
        }

        $settings = InterfacePackagingSetting::instance();
        $settings->update([
            'barcode_enabled' => (bool) ($data['barcode_enabled'] ?? false),
            'barcode_label' => trim((string) ($data['barcode_label'] ?? '')) ?: 'Barkod / Etiket Alanı İstiyorum',
            'barcode_extra_price' => max(0, (float) ($data['barcode_extra_price'] ?? 0)),
            'barcode_description' => filled($data['barcode_description'] ?? null) ? trim((string) $data['barcode_description']) : null,
            'barcode_image_path' => $data['barcode_image_path'] ?? null,
            'customizations_enabled' => (bool) ($data['customizations_enabled'] ?? true),
        ]);

        $notification = Notification::make()
            ->title('Ambalaj tercihleri kaydedildi')
            ->success();

        $productChanges = ($productSync['added'] ?? 0) + ($productSync['updated'] ?? 0) + ($productSync['removed'] ?? 0);
        if ($productChanges > 0) {
            $notification->body('Ürün ambalaj varyasyonları senkronize edildi.');
        }

        $notification->send();

        $this->mount();
    }
}
