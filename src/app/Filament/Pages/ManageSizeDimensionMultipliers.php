<?php

namespace App\Filament\Pages;

use App\Models\ColorDimensionMultiplier;
use App\Models\ProductCustomizationSetting;
use App\Models\QuantityDimensionMultiplier;
use App\Models\SizeDimensionMultiplier;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Model;

class ManageSizeDimensionMultipliers extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';

    protected static ?string $navigationLabel = 'Ebat Çarpan Yönetimi';

    protected static ?string $title = 'Ebat Çarpan Yönetimi';

    protected static ?string $navigationGroup = 'Varyasyon yönetimi';

    protected static ?int $navigationSort = 4;

    protected static string $view = 'filament.pages.manage-size-dimension-multipliers';

    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    public static function getSlug(): string
    {
        return 'size-dimension-multipliers';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('backToCustomization')
                ->label('Ürün Özelleştirme')
                ->icon('heroicon-o-arrow-left')
                ->url(fn (): string => ManageProductCustomization::getUrl())
                ->color('gray'),
        ];
    }

    public function mount(): void
    {
        $this->form->fill([
            'size_rows' => $this->loadSizeRows(),
            'color_rows' => $this->loadColorRows(),
            'quantity_rows' => $this->loadQuantityRows(),
        ]);
    }

    public function form(Form $form): Form
    {
        $maxColors = $this->maxColorCount();

        return $form
            ->schema([
                Forms\Components\Section::make('Ebat Çarpanı')
                    ->description('Ebat bazlı otomatik, sabit ve ekstra çarpan değerleri.')
                    ->schema([
                        $this->sizeMultiplierTableRepeater('size_rows', 'Henüz ebat satırı yok. Aşağıdan satır ekleyin.'),
                    ]),
                Forms\Components\Section::make('Renk Çarpanı')
                    ->description('Renk sayısı seçenekleri Ürün Özelleştirme sayfasındaki maksimum renk sayısından otomatik gelir (1–'.$maxColors.').')
                    ->schema([
                        $this->colorMultiplierTableRepeater('color_rows', 'Henüz renk çarpanı satırı yok. Aşağıdan satır ekleyin.'),
                    ]),
                Forms\Components\Section::make('Adet Çarpanı')
                    ->description('Başlangıç ve bitiş adetini 1–1000 arasında girin; bitiş, başlangıçtan küçük olamaz.')
                    ->schema([
                        $this->quantityMultiplierTableRepeater('quantity_rows', 'Henüz adet çarpanı satırı yok. Aşağıdan satır ekleyin.'),
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

        $this->persistSizeRows($data['size_rows'] ?? []);
        $this->persistColorRows($data['color_rows'] ?? []);
        $this->persistQuantityRows($data['quantity_rows'] ?? []);

        Notification::make()
            ->title('Çarpan tabloları kaydedildi')
            ->success()
            ->send();

        $this->mount();
    }

    private function sizeMultiplierTableRepeater(string $name, string $emptyMessage): Repeater
    {
        return Repeater::make($name)
            ->label('')
            ->view('filament.forms.components.form-table-repeater')
            ->viewData([
                'tableHeaders' => [
                    'EBAT',
                    'EN (cm)',
                    'BOY (cm)',
                    'Ebat cm²',
                    'SABİT ÇARPAN',
                    'EKSTRA ÇARPAN',
                    ['label' => 'Aktif', 'align' => 'center'],
                ],
                'emptyMessage' => $emptyMessage,
                'tableMinWidth' => '56rem',
            ])
            ->schema([
                Forms\Components\Hidden::make('id'),
                Forms\Components\TextInput::make('size_label')
                    ->label('EBAT')
                    ->required()
                    ->maxLength(64)
                    ->hiddenLabel(),
                Forms\Components\TextInput::make('width')
                    ->label('EN (cm)')
                    ->numeric()
                    ->minValue(0)
                    ->step(0.01)
                    ->hiddenLabel(),
                Forms\Components\TextInput::make('height')
                    ->label('BOY (cm)')
                    ->numeric()
                    ->minValue(0)
                    ->step(0.01)
                    ->hiddenLabel(),
                Forms\Components\TextInput::make('auto_multiplier')
                    ->label('Ebat cm²')
                    ->numeric()
                    ->minValue(0)
                    ->step(0.01)
                    ->default(1)
                    ->required()
                    ->extraInputAttributes(['step' => '0.01'])
                    ->hiddenLabel(),
                Forms\Components\TextInput::make('fixed_multiplier')
                    ->label('SABİT ÇARPAN')
                    ->maxLength(64)
                    ->nullable()
                    ->placeholder('SABİT FİYAT')
                    ->hiddenLabel(),
                Forms\Components\TextInput::make('extra_multiplier')
                    ->label('EKSTRA ÇARPAN')
                    ->numeric()
                    ->step(0.0001)
                    ->default(0)
                    ->required()
                    ->hiddenLabel(),
                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true)
                    ->inline(false)
                    ->hiddenLabel(),
            ])
            ->defaultItems(0)
            ->addActionLabel('Satır ekle')
            ->reorderableWithDragAndDrop()
            ->collapsible(false)
            ->cloneable(false);
    }

    private function colorMultiplierTableRepeater(string $name, string $emptyMessage): Repeater
    {
        return Repeater::make($name)
            ->label('')
            ->view('filament.forms.components.form-table-repeater')
            ->viewData([
                'tableHeaders' => [
                    'Renk Sayısı Seç',
                    'Çarpan Fiyatı',
                    ['label' => 'Aktif', 'align' => 'center'],
                ],
                'emptyMessage' => $emptyMessage,
                'tableMinWidth' => '28rem',
            ])
            ->schema([
                Forms\Components\Hidden::make('id'),
                Forms\Components\Select::make('color_count')
                    ->label('Renk Sayısı Seç')
                    ->options(fn (): array => $this->colorCountSelectOptions())
                    ->required()
                    ->native(true)
                    ->hiddenLabel(),
                Forms\Components\TextInput::make('multiplier_price')
                    ->label('Çarpan Fiyatı')
                    ->numeric()
                    ->minValue(0)
                    ->step(0.0001)
                    ->default(0)
                    ->required()
                    ->hiddenLabel(),
                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true)
                    ->inline(false)
                    ->hiddenLabel(),
            ])
            ->defaultItems(0)
            ->addActionLabel('Satır ekle')
            ->reorderableWithDragAndDrop()
            ->collapsible(false)
            ->cloneable(false);
    }

    private function quantityMultiplierTableRepeater(string $name, string $emptyMessage): Repeater
    {
        return Repeater::make($name)
            ->label('')
            ->view('filament.forms.components.form-table-repeater')
            ->viewData([
                'tableHeaders' => [
                    'Başlangıç',
                    'Bitiş',
                    'Çarpan Fiyatı',
                    ['label' => 'Aktif', 'align' => 'center'],
                ],
                'emptyMessage' => $emptyMessage,
                'tableMinWidth' => '36rem',
            ])
            ->schema([
                Forms\Components\Hidden::make('id'),
                Forms\Components\TextInput::make('quantity_from')
                    ->label('Başlangıç')
                    ->numeric()
                    ->integer()
                    ->minValue(1)
                    ->maxValue(1000)
                    ->required()
                    ->default(1)
                    ->extraInputAttributes(['min' => 1, 'max' => 1000, 'step' => 1, 'inputmode' => 'numeric'])
                    ->hiddenLabel(),
                Forms\Components\TextInput::make('quantity_to')
                    ->label('Bitiş')
                    ->numeric()
                    ->integer()
                    ->minValue(1)
                    ->maxValue(1000)
                    ->required()
                    ->default(1)
                    ->extraInputAttributes(['min' => 1, 'max' => 1000, 'step' => 1, 'inputmode' => 'numeric'])
                    ->hiddenLabel(),
                Forms\Components\TextInput::make('multiplier_price')
                    ->label('Çarpan Fiyatı')
                    ->numeric()
                    ->minValue(0)
                    ->step(0.0001)
                    ->default(0)
                    ->required()
                    ->hiddenLabel(),
                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true)
                    ->inline(false)
                    ->hiddenLabel(),
            ])
            ->defaultItems(0)
            ->addActionLabel('Satır ekle')
            ->reorderableWithDragAndDrop()
            ->collapsible(false)
            ->cloneable(false);
    }

    /** @return list<array<string, mixed>> */
    private function loadSizeRows(): array
    {
        return SizeDimensionMultiplier::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(function (SizeDimensionMultiplier $row): array {
                $data = SizeDimensionMultiplier::repeaterRowFromModel($row);
                $data['auto_multiplier'] = number_format((float) ($data['auto_multiplier'] ?? 0), 2, '.', '');

                return $data;
            })
            ->values()
            ->all();
    }

    /** @return list<array<string, mixed>> */
    private function loadColorRows(): array
    {
        return ColorDimensionMultiplier::query()
            ->orderBy('sort_order')
            ->orderBy('color_count')
            ->get()
            ->map(fn (ColorDimensionMultiplier $row): array => ColorDimensionMultiplier::repeaterRowFromModel($row))
            ->values()
            ->all();
    }

    /** @return list<array<string, mixed>> */
    private function loadQuantityRows(): array
    {
        return QuantityDimensionMultiplier::query()
            ->orderBy('sort_order')
            ->orderBy('quantity_from')
            ->get()
            ->map(fn (QuantityDimensionMultiplier $row): array => QuantityDimensionMultiplier::repeaterRowFromModel($row))
            ->values()
            ->all();
    }

    /** @param  list<array<string, mixed>>  $rows */
    private function persistSizeRows(array $rows): void
    {
        $keptIds = [];
        $sort = 0;

        foreach ($rows as $row) {
            $label = trim((string) ($row['size_label'] ?? ''));
            if ($label === '') {
                continue;
            }

            $attrs = [
                'size_label' => $label,
                'width' => filled($row['width'] ?? null) ? $row['width'] : null,
                'height' => filled($row['height'] ?? null) ? $row['height'] : null,
                'auto_multiplier' => $this->normalizeDecimal($row['auto_multiplier'] ?? 1, 1, 2),
                'fixed_multiplier' => $this->normalizeFixedMultiplier($row['fixed_multiplier'] ?? null),
                'extra_multiplier' => $this->normalizeDecimal($row['extra_multiplier'] ?? 0, 0),
                'sort_order' => $sort * 10,
                'is_active' => (bool) ($row['is_active'] ?? true),
            ];

            $keptIds[] = $this->upsertRow(SizeDimensionMultiplier::class, $row, $attrs);
            $sort++;
        }

        $this->deleteRemovedRows(SizeDimensionMultiplier::class, $keptIds);
    }

    /** @param  list<array<string, mixed>>  $rows */
    private function persistColorRows(array $rows): void
    {
        $keptIds = [];
        $sort = 0;
        $maxColors = $this->maxColorCount();

        foreach ($rows as $row) {
            $colorCount = (int) ($row['color_count'] ?? 0);
            if ($colorCount < 1 || $colorCount > $maxColors) {
                continue;
            }

            $attrs = [
                'color_count' => $colorCount,
                'multiplier_price' => $this->normalizeDecimal($row['multiplier_price'] ?? 0, 0),
                'sort_order' => $sort * 10,
                'is_active' => (bool) ($row['is_active'] ?? true),
            ];

            $keptIds[] = $this->upsertRow(ColorDimensionMultiplier::class, $row, $attrs);
            $sort++;
        }

        $this->deleteRemovedRows(ColorDimensionMultiplier::class, $keptIds);
    }

    /** @param  list<array<string, mixed>>  $rows */
    private function persistQuantityRows(array $rows): void
    {
        $keptIds = [];
        $sort = 0;

        foreach ($rows as $row) {
            $from = (int) ($row['quantity_from'] ?? 0);
            $to = (int) ($row['quantity_to'] ?? 0);
            if ($from < 1 || $from > 1000 || $to < 1 || $to > 1000 || $to < $from) {
                continue;
            }

            $attrs = [
                'quantity_from' => $from,
                'quantity_to' => $to,
                'multiplier_price' => $this->normalizeDecimal($row['multiplier_price'] ?? 0, 0),
                'sort_order' => $sort * 10,
                'is_active' => (bool) ($row['is_active'] ?? true),
            ];

            $keptIds[] = $this->upsertRow(QuantityDimensionMultiplier::class, $row, $attrs);
            $sort++;
        }

        $this->deleteRemovedRows(QuantityDimensionMultiplier::class, $keptIds);
    }

    /**
     * @param  class-string<Model>  $modelClass
     * @param  array<string, mixed>  $row
     * @param  array<string, mixed>  $attrs
     */
    private function upsertRow(string $modelClass, array $row, array $attrs): int
    {
        if (! empty($row['id'])) {
            $model = $modelClass::query()->find((int) $row['id']);
            if ($model) {
                $model->update($attrs);

                return $model->id;
            }
        }

        $model = $modelClass::query()->create($attrs);

        return $model->id;
    }

    /**
     * @param  class-string<Model>  $modelClass
     * @param  list<int>  $keptIds
     */
    private function deleteRemovedRows(string $modelClass, array $keptIds): void
    {
        $modelClass::query()
            ->when($keptIds !== [], fn ($q) => $q->whereNotIn('id', $keptIds))
            ->delete();
    }

    /** @return array<int, string> */
    private function colorCountSelectOptions(): array
    {
        $max = $this->maxColorCount();
        $options = [];

        for ($i = 1; $i <= $max; $i++) {
            $options[$i] = $i.' renk';
        }

        return $options;
    }

    private function maxColorCount(): int
    {
        return max(1, min(20, (int) ProductCustomizationSetting::instance()->max_color_count));
    }

    private function normalizeDecimal(mixed $value, float $fallback, int $decimals = 4): float
    {
        if (! is_numeric($value)) {
            return $fallback;
        }

        return max(0, round((float) $value, $decimals));
    }

    private function normalizeFixedMultiplier(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $raw = trim((string) $value);
        if ($raw === '') {
            return null;
        }

        $numeric = str_replace(',', '.', $raw);
        if (is_numeric($numeric) && ! preg_match('/[a-zA-ZğüşıöçĞÜŞİÖÇ]/u', $raw)) {
            return (string) max(0, round((float) $numeric, 4));
        }

        return $raw;
    }
}
