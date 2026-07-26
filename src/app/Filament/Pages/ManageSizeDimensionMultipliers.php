<?php

namespace App\Filament\Pages;

use App\Models\ProductCustomizationSetting;
use App\Support\PrintTechniqueDimensionMultiplierTypes;
use App\Support\PrintTechniqueMultiplierTabs;
use App\Support\ProductDimensionMultiplierSync;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ManageSizeDimensionMultipliers extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';

    protected static ?string $navigationLabel = 'Varsayılan çarpan şablonu';

    protected static ?string $title = 'Varsayılan çarpan şablonu';

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
        $this->form->fill(ProductDimensionMultiplierSync::loadGroupedForForm(null, fallbackToTemplate: false));
    }

    public function form(Form $form): Form
    {
        $maxColors = $this->maxColorCount();
        $emprimeLabel = PrintTechniqueMultiplierTabs::labelsBySlug()[PrintTechniqueDimensionMultiplierTypes::SLUG_EMPRIME] ?? 'Emprime';

        $tabs = [];
        foreach (PrintTechniqueMultiplierTabs::definitions() as $printTechnique) {
            $slug = $printTechnique['slug'];
            $label = $printTechnique['label'];
            $tabSchema = [
                Forms\Components\Section::make('Ebat Çarpanı')
                    ->description($label.' için ebat bazlı otomatik, sabit ve ekstra çarpan değerleri.')
                    ->schema([
                        $this->sizeMultiplierTableRepeater("{$slug}.size_rows", 'Henüz ebat satırı yok. Aşağıdan satır ekleyin.'),
                    ]),
            ];

            if (PrintTechniqueDimensionMultiplierTypes::supportsColorMultiplier($slug)) {
                $tabSchema[] = Forms\Components\Section::make('Renk Çarpanı')
                    ->description('Renk sayısı seçenekleri Ürün Özelleştirme sayfasındaki maksimum renk sayısından otomatik gelir (1–'.$maxColors.'). Sadece '.$emprimeLabel.' baskıda uygulanır.')
                    ->schema([
                        $this->colorMultiplierTableRepeater("{$slug}.color_rows", 'Henüz renk çarpanı satırı yok. Aşağıdan satır ekleyin.'),
                    ]);
            }

            $tabSchema[] = Forms\Components\Section::make('Adet Çarpanı')
                ->description($label.' için başlangıç ve bitiş adetini 1–1000 arasında girin; bitiş, başlangıçtan küçük olamaz.')
                ->schema([
                    $this->quantityMultiplierTableRepeater("{$slug}.quantity_rows", 'Henüz adet çarpanı satırı yok. Aşağıdan satır ekleyin.'),
                ]);

            $tabs[] = Tabs\Tab::make($slug)
                ->label($label)
                ->schema($tabSchema);
        }

        return $form
            ->schema([
                Tabs::make('print_technique_tabs')
                    ->tabs($tabs)
                    ->persistTabInQueryString('baski'),
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
        ProductDimensionMultiplierSync::persistGrouped(null, $this->form->getState());

        Notification::make()
            ->title('Varsayılan çarpan şablonu kaydedildi')
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
}
