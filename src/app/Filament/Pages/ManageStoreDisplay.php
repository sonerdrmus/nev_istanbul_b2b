<?php

namespace App\Filament\Pages;

use App\Models\StoreSetting;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ManageStoreDisplay extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-eye';

    protected static ?string $navigationLabel = 'Fiyat çarpanı görünümü';

    protected static ?string $title = 'Fiyat çarpanı görünümü';

    protected static ?string $navigationGroup = 'E-Ticaret';

    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.pages.manage-store-display';

    public ?array $data = [];

    public static function getSlug(): string
    {
        return 'store-display';
    }

    public function mount(): void
    {
        $settings = StoreSetting::current();

        $this->form->fill([
            'show_price_multipliers' => $settings->show_price_multipliers,
            'show_print_total' => $settings->show_print_total,
            'show_print_price' => $settings->show_print_price,
            'show_print_card_total' => $settings->show_print_card_total,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Mağaza ürün sayfası')
                    ->description('×1,19 gibi fiyat çarpanı etiketleri. Kapalıyken müşteri bu etiketleri görmez; fiyat hesabı aynı şekilde uygulanır.')
                    ->schema([
                        Forms\Components\Toggle::make('show_price_multipliers')
                            ->label('Fiyat çarpanlarını göster')
                            ->helperText('Açıkken seçim özeti, teslimat alt seçenekleri ve özelleştirme kartındaki çarpan alanları görünür.'),
                        Forms\Components\Toggle::make('show_print_total')
                            ->label('Baskı toplamını göster')
                            ->helperText('Açıkken ürün sayfasında Baskı toplamı, adet çarpımı ve açıklama satırı görünür. Kapalıyken bu kutu gizlenir; baskı tutarı sipariş hesabına eklenmeye devam eder.'),
                        Forms\Components\Toggle::make('show_print_price')
                            ->label('Baskı kartında Price göster')
                            ->helperText('Print customization kartındaki Price alanı. Kapalıyken gizlenir.'),
                        Forms\Components\Toggle::make('show_print_card_total')
                            ->label('Baskı kartında Total price göster')
                            ->helperText('Print customization kartındaki Total price alanı. Kapalıyken gizlenir; tutar hesaba eklenmeye devam eder.'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        StoreSetting::current()->update([
            'show_price_multipliers' => (bool) ($state['show_price_multipliers'] ?? false),
            'show_print_total' => (bool) ($state['show_print_total'] ?? false),
            'show_print_price' => (bool) ($state['show_print_price'] ?? false),
            'show_print_card_total' => (bool) ($state['show_print_card_total'] ?? false),
        ]);

        Notification::make()
            ->title('Kaydedildi')
            ->success()
            ->send();
    }
}
