<?php

namespace App\Filament\Forms;

use App\Models\ProductCustomizationSetting;
use App\Support\PrintTechniqueDimensionMultiplierTypes;
use App\Support\PrintTechniqueMultiplierTabs;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Tabs;

/**
 * Ürün formundaki ebat/renk/adet çarpan sekmeleri (ManageSizeDimensionMultipliers ile aynı şema).
 */
final class ProductDimensionMultiplierFormSchema
{
    public static function section(): Forms\Components\Section
    {
        $maxColors = max(1, min(20, (int) ProductCustomizationSetting::instance()->max_color_count));
        $emprimeLabel = PrintTechniqueMultiplierTabs::labelsBySlug()[PrintTechniqueDimensionMultiplierTypes::SLUG_EMPRIME] ?? 'Emprime';

        $tabs = [];
        foreach (PrintTechniqueMultiplierTabs::definitions() as $printTechnique) {
            $slug = $printTechnique['slug'];
            $label = $printTechnique['label'];
            $tabSchema = [
                Forms\Components\Section::make('Ebat Çarpanı')
                    ->description($label.' için ebat bazlı otomatik, sabit ve ekstra çarpan değerleri.')
                    ->schema([
                        static::sizeRepeater("{$slug}.size_rows"),
                    ]),
            ];

            if (PrintTechniqueDimensionMultiplierTypes::supportsColorMultiplier($slug)) {
                $tabSchema[] = Forms\Components\Section::make('Renk Çarpanı')
                    ->description('Sadece '.$emprimeLabel.' baskıda uygulanır (1–'.$maxColors.' renk).')
                    ->schema([
                        static::colorRepeater("{$slug}.color_rows", $maxColors),
                    ]);
            }

            $tabSchema[] = Forms\Components\Section::make('Adet Çarpanı')
                ->description($label.' için başlangıç ve bitiş adeti (1–1000).')
                ->schema([
                    static::quantityRepeater("{$slug}.quantity_rows"),
                ]);

            $tabs[] = Tabs\Tab::make($slug)
                ->label($label)
                ->schema($tabSchema);
        }

        return Forms\Components\Section::make('Bu ürüne özel çarpanlar')
            ->description('Boş bırakılırsa kayıtta varsayılan şablon bu ürüne kopyalanır. Şablon: Varyasyon yönetimi → Ürün Özelleştirme → Çarpan Yönetimi.')
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Tabs::make('product_print_technique_tabs')
                            ->tabs($tabs)
                            ->columnSpanFull(),
                    ])
                    ->statePath('dimension_multipliers'),
            ])
            ->collapsed(false)
            ->columnSpanFull();
    }

    private static function sizeRepeater(string $name): Repeater
    {
        return Repeater::make($name)
            ->label('')
            ->view('filament.forms.components.form-table-repeater')
            ->viewData([
                'tableHeaders' => [
                    'EBAT', 'EN (cm)', 'BOY (cm)', 'Ebat cm²', 'SABİT ÇARPAN', 'EKSTRA ÇARPAN',
                    ['label' => 'Aktif', 'align' => 'center'],
                ],
                'emptyMessage' => 'Henüz ebat satırı yok.',
                'tableMinWidth' => '56rem',
            ])
            ->schema([
                Forms\Components\Hidden::make('id'),
                Forms\Components\TextInput::make('size_label')->label('EBAT')->required()->maxLength(64)->hiddenLabel(),
                Forms\Components\TextInput::make('width')->label('EN (cm)')->numeric()->minValue(0)->step(0.01)->hiddenLabel(),
                Forms\Components\TextInput::make('height')->label('BOY (cm)')->numeric()->minValue(0)->step(0.01)->hiddenLabel(),
                Forms\Components\TextInput::make('auto_multiplier')->label('Ebat cm²')->numeric()->minValue(0)->step(0.01)->default(1)->required()->hiddenLabel(),
                Forms\Components\TextInput::make('fixed_multiplier')->label('SABİT ÇARPAN')->maxLength(64)->nullable()->hiddenLabel(),
                Forms\Components\TextInput::make('extra_multiplier')->label('EKSTRA ÇARPAN')->numeric()->step(0.001)->default(0)->required()->hiddenLabel(),
                Forms\Components\Toggle::make('is_active')->label('Aktif')->default(true)->inline(false)->hiddenLabel(),
            ])
            ->defaultItems(0)
            ->addActionLabel('Satır ekle')
            ->reorderableWithDragAndDrop()
            ->collapsible(false)
            ->cloneable(false);
    }

    private static function colorRepeater(string $name, int $maxColors): Repeater
    {
        $options = [];
        for ($i = 1; $i <= $maxColors; $i++) {
            $options[$i] = $i.' renk';
        }

        return Repeater::make($name)
            ->label('')
            ->view('filament.forms.components.form-table-repeater')
            ->viewData([
                'tableHeaders' => ['Renk Sayısı Seç', 'Çarpan Fiyatı', ['label' => 'Aktif', 'align' => 'center']],
                'emptyMessage' => 'Henüz renk çarpanı yok.',
                'tableMinWidth' => '28rem',
            ])
            ->schema([
                Forms\Components\Hidden::make('id'),
                Forms\Components\Select::make('color_count')->label('Renk Sayısı Seç')->options($options)->required()->native(true)->hiddenLabel(),
                Forms\Components\TextInput::make('multiplier_price')->label('Çarpan Fiyatı')->numeric()->minValue(0)->step(0.001)->default(0)->required()->hiddenLabel(),
                Forms\Components\Toggle::make('is_active')->label('Aktif')->default(true)->inline(false)->hiddenLabel(),
            ])
            ->defaultItems(0)
            ->addActionLabel('Satır ekle')
            ->reorderableWithDragAndDrop()
            ->collapsible(false)
            ->cloneable(false);
    }

    private static function quantityRepeater(string $name): Repeater
    {
        return Repeater::make($name)
            ->label('')
            ->view('filament.forms.components.form-table-repeater')
            ->viewData([
                'tableHeaders' => ['Başlangıç', 'Bitiş', 'Çarpan Fiyatı', ['label' => 'Aktif', 'align' => 'center']],
                'emptyMessage' => 'Henüz adet çarpanı yok.',
                'tableMinWidth' => '36rem',
            ])
            ->schema([
                Forms\Components\Hidden::make('id'),
                Forms\Components\TextInput::make('quantity_from')->label('Başlangıç')->numeric()->integer()->minValue(1)->maxValue(1000)->required()->default(1)->hiddenLabel(),
                Forms\Components\TextInput::make('quantity_to')->label('Bitiş')->numeric()->integer()->minValue(1)->maxValue(1000)->required()->default(1)->hiddenLabel(),
                Forms\Components\TextInput::make('multiplier_price')->label('Çarpan Fiyatı')->numeric()->minValue(0)->step(0.001)->default(0)->required()->hiddenLabel(),
                Forms\Components\Toggle::make('is_active')->label('Aktif')->default(true)->inline(false)->hiddenLabel(),
            ])
            ->defaultItems(0)
            ->addActionLabel('Satır ekle')
            ->reorderableWithDragAndDrop()
            ->collapsible(false)
            ->cloneable(false);
    }
}
