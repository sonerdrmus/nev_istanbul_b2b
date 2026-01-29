<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\CompanyResource;
use App\Filament\Resources\DealerRequestResource;
use App\Filament\Resources\OrderResource;
use App\Filament\Resources\ProductResource;
use App\Filament\Resources\UserResource;
use App\Models\Company;
use App\Models\DealerRequest;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class DashboardStatsWidget extends BaseWidget
{
    protected static ?int $sort = 0;

    protected int | string | array $columnSpan = 'full';

    protected ?string $heading = 'Özet Rapor';

    protected ?string $description = 'B2B paneli anahtar göstergeleri.';

    protected function getStats(): array
    {
        $panelId = 'admin';

        $bayiCount = User::whereNotNull('company_id')->count();
        $orderCount = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $dealerRequests = DealerRequest::count();
        $pendingDealerRequests = DealerRequest::where('status', 'pending')->count();
        $companyCount = Company::count();
        $productCount = Product::active()->visibleInStore()->count();

        $thisMonthOrders = Order::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year);
        $monthlyTotal = (float) $thisMonthOrders->sum('total');
        $monthlyOrderCount = $thisMonthOrders->count();

        return [
            Stat::make('Bayi Sayısı', $bayiCount)
                ->description('Kayıtlı bayi kullanıcıları')
                ->descriptionIcon('heroicon-m-user-group')
                ->icon('heroicon-o-user-group')
                ->color('primary')
                ->url(UserResource::getUrl(panel: $panelId)),

            Stat::make('Şirket Sayısı', $companyCount)
                ->description('Kayıtlı şirketler')
                ->descriptionIcon('heroicon-m-building-office')
                ->icon('heroicon-o-building-office-2')
                ->color('info')
                ->url(CompanyResource::getUrl(panel: $panelId)),

            Stat::make('Toplam Sipariş', $orderCount)
                ->description($pendingOrders > 0 ? "Bekleyen: {$pendingOrders}" : 'Tüm siparişler')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->icon('heroicon-o-shopping-cart')
                ->color($pendingOrders > 0 ? 'warning' : 'success')
                ->url(OrderResource::getUrl(panel: $panelId)),

            Stat::make('Bu Ay Sipariş', $monthlyOrderCount)
                ->description(number_format($monthlyTotal, 2, ',', '.') . ' ₺')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->icon('heroicon-o-banknotes')
                ->color('success'),

            Stat::make('Bayilik Talepleri', $dealerRequests)
                ->description($pendingDealerRequests > 0 ? "Bekleyen: {$pendingDealerRequests}" : 'Tüm talepler')
                ->descriptionIcon('heroicon-m-document-text')
                ->icon('heroicon-o-clipboard-document-list')
                ->color($pendingDealerRequests > 0 ? 'warning' : 'gray')
                ->url(DealerRequestResource::getUrl(panel: $panelId)),

            Stat::make('Ürün Sayısı', $productCount)
                ->description('Mağazada listelenen ürünler')
                ->descriptionIcon('heroicon-m-cube')
                ->icon('heroicon-o-cube')
                ->color('gray')
                ->url(ProductResource::getUrl(panel: $panelId)),
        ];
    }
}
