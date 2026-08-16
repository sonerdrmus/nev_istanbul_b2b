<?php

namespace App\Providers\Filament;

use App\Filament\Resources\BannerSlideResource;
use App\Filament\Resources\CategoryResource;
use App\Filament\Pages\ManagePackagingPreferences;
use App\Filament\Pages\ManageProductCustomization;
use App\Filament\Resources\InterfaceCertificateVariationResource;
use App\Filament\Resources\InterfaceColorVariationResource;
use App\Filament\Resources\InterfaceDeliveryMethodVariationResource;
use App\Filament\Resources\InterfaceFabricTypeVariationResource;
use App\Filament\Resources\InterfaceLabelTypeVariationResource;
use App\Filament\Resources\InterfaceMoldModelVariationResource;
use App\Filament\Resources\FooterMenuGroupResource;
use App\Filament\Resources\FooterSettingResource;
use App\Filament\Resources\LegalPageResource;
use App\Filament\Resources\ProductResource;
use App\Filament\Resources\SizeTableResource;
use App\Filament\Resources\TaxClassResource;
use App\Filament\Resources\TaxRateResource;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('NEVISTANBUL')
            ->brandLogo(asset('images/nevistanbul-logo-beyaz.png'))
            ->brandLogoHeight('2.25rem')
            ->darkModeBrandLogo(asset('images/nevistanbul-logo-beyaz.png'))
            ->sidebarCollapsibleOnDesktop()
            ->sidebarWidth('16rem')
            ->collapsedSidebarWidth('5rem')
            ->colors([
                'primary' => '#184E77',
                'success' => '#059669',
                'danger' => Color::Rose,
                'gray' => Color::Slate,
                'info' => '#0e7490',
                'warning' => Color::Amber,
            ])
            ->font('Plus Jakarta Sans')
            ->renderHook(PanelsRenderHook::STYLES_AFTER, fn (): string => '<link rel="stylesheet" href="' . e(asset('css/filament-admin-custom.css')) . '">')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->navigationGroups([
                'E-Ticaret',
                'Varyasyon yönetimi',
                'B2B Yönetimi',
            ])
            ->navigationItems(static::getSidebarSubMenuItems())
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                \App\Filament\Widgets\DashboardStatsWidget::class,
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    /**
     * Sol menüde alt menülü (Ürünler, Kategoriler) öğeleri.
     *
     * @return array<NavigationItem>
     */
    protected static function getSidebarSubMenuItems(): array
    {
        $panelId = 'admin';

        return [
            NavigationItem::make('Ürünler')
                ->group('E-Ticaret')
                ->icon('heroicon-o-shopping-bag')
                ->url(fn (): string => ProductResource::getUrl(panel: $panelId))
                ->sort(1)
                ->isActiveWhen(fn (): bool => request()->routeIs(ProductResource::getRouteBaseName($panelId) . '.*'))
                ->childItems([
                    NavigationItem::make('Ürün Listesi')
                        ->url(fn (): string => ProductResource::getUrl(panel: $panelId))
                        ->icon('heroicon-o-list-bullet')
                        ->isActiveWhen(fn (): bool => request()->routeIs(ProductResource::getRouteBaseName($panelId) . '.index')),
                    NavigationItem::make('Yeni Ürün')
                        ->url(fn (): string => ProductResource::getUrl('create', panel: $panelId))
                        ->icon('heroicon-o-plus')
                        ->isActiveWhen(fn (): bool => request()->routeIs(ProductResource::getRouteBaseName($panelId) . '.create')),
                ]),
            NavigationItem::make('Kategoriler')
                ->group('E-Ticaret')
                ->icon('heroicon-o-folder')
                ->url(fn (): string => CategoryResource::getUrl(panel: $panelId))
                ->sort(2)
                ->isActiveWhen(fn (): bool => request()->routeIs(CategoryResource::getRouteBaseName($panelId) . '.*'))
                ->childItems([
                    NavigationItem::make('Kategori Listesi')
                        ->url(fn (): string => CategoryResource::getUrl(panel: $panelId))
                        ->icon('heroicon-o-list-bullet')
                        ->isActiveWhen(fn (): bool => request()->routeIs(CategoryResource::getRouteBaseName($panelId) . '.index')),
                    NavigationItem::make('Yeni Kategori')
                        ->url(fn (): string => CategoryResource::getUrl('create', panel: $panelId))
                        ->icon('heroicon-o-plus')
                        ->isActiveWhen(fn (): bool => request()->routeIs(CategoryResource::getRouteBaseName($panelId) . '.create')),
                ]),
            NavigationItem::make('Vergi Yönetimi')
                ->group('E-Ticaret')
                ->icon('heroicon-o-calculator')
                ->url(fn (): string => TaxClassResource::getUrl(panel: $panelId))
                ->sort(3)
                ->isActiveWhen(fn (): bool => request()->routeIs(TaxClassResource::getRouteBaseName($panelId) . '.*') || request()->routeIs(TaxRateResource::getRouteBaseName($panelId) . '.*'))
                ->childItems([
                    NavigationItem::make('Vergi Sınıfları')
                        ->url(fn (): string => TaxClassResource::getUrl(panel: $panelId))
                        ->icon('heroicon-o-rectangle-stack')
                        ->isActiveWhen(fn (): bool => request()->routeIs(TaxClassResource::getRouteBaseName($panelId) . '.*')),
                    NavigationItem::make('Vergi Oranları')
                        ->url(fn (): string => TaxRateResource::getUrl(panel: $panelId))
                        ->icon('heroicon-o-percent-badge')
                        ->isActiveWhen(fn (): bool => request()->routeIs(TaxRateResource::getRouteBaseName($panelId) . '.*')),
                ]),
            NavigationItem::make('Varyasyon yönetimi')
                ->group('Varyasyon yönetimi')
                ->icon('heroicon-o-adjustments-horizontal')
                ->url(fn (): string => InterfaceColorVariationResource::getUrl(panel: $panelId))
                ->sort(1)
                ->isActiveWhen(fn (): bool => request()->routeIs(InterfaceColorVariationResource::getRouteBaseName($panelId) . '.*')
                    || request()->routeIs(InterfaceFabricTypeVariationResource::getRouteBaseName($panelId) . '.*')
                    || request()->routeIs(InterfaceLabelTypeVariationResource::getRouteBaseName($panelId) . '.*')
                    || request()->routeIs(InterfaceCertificateVariationResource::getRouteBaseName($panelId) . '.*')
                    || request()->routeIs(InterfaceMoldModelVariationResource::getRouteBaseName($panelId) . '.*')
                    || request()->routeIs(InterfaceDeliveryMethodVariationResource::getRouteBaseName($panelId) . '.*')
                    || request()->routeIs('filament.admin.pages.packaging-preferences')
                    || request()->routeIs(SizeTableResource::getRouteBaseName($panelId) . '.*')
                    || request()->routeIs('filament.admin.pages.product-customization')
                    || request()->routeIs('filament.admin.pages.size-dimension-multipliers'))
                ->childItems([
                    NavigationItem::make('Renk Varyasyonları')
                        ->url(fn (): string => InterfaceColorVariationResource::getUrl(panel: $panelId))
                        ->icon('heroicon-o-swatch')
                        ->isActiveWhen(fn (): bool => request()->routeIs(InterfaceColorVariationResource::getRouteBaseName($panelId) . '.*')),
                    NavigationItem::make('Kumaş Türü Varyasyonları')
                        ->url(fn (): string => InterfaceFabricTypeVariationResource::getUrl(panel: $panelId))
                        ->icon('heroicon-o-queue-list')
                        ->isActiveWhen(fn (): bool => request()->routeIs(InterfaceFabricTypeVariationResource::getRouteBaseName($panelId) . '.*')),
                    NavigationItem::make('Beden tabloları')
                        ->url(fn (): string => SizeTableResource::getUrl(panel: $panelId))
                        ->icon('heroicon-o-table-cells')
                        ->isActiveWhen(fn (): bool => request()->routeIs(SizeTableResource::getRouteBaseName($panelId) . '.*')),
                    NavigationItem::make('Etiket Türü Yönetimi')
                        ->url(fn (): string => InterfaceLabelTypeVariationResource::getUrl(panel: $panelId))
                        ->icon('heroicon-o-tag')
                        ->isActiveWhen(fn (): bool => request()->routeIs(InterfaceLabelTypeVariationResource::getRouteBaseName($panelId) . '.*')),
                    NavigationItem::make('Ambalaj Tercih Yönetimi')
                        ->url(fn (): string => ManagePackagingPreferences::getUrl(panel: $panelId))
                        ->icon('heroicon-o-gift')
                        ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.pages.packaging-preferences')),
                    NavigationItem::make('Sertifika Yönetimi')
                        ->url(fn (): string => InterfaceCertificateVariationResource::getUrl(panel: $panelId))
                        ->icon('heroicon-o-document-check')
                        ->isActiveWhen(fn (): bool => request()->routeIs(InterfaceCertificateVariationResource::getRouteBaseName($panelId) . '.*')),
                    NavigationItem::make('Kalıp Modeli Yönetimi')
                        ->url(fn (): string => InterfaceMoldModelVariationResource::getUrl(panel: $panelId))
                        ->icon('heroicon-o-cube')
                        ->isActiveWhen(fn (): bool => request()->routeIs(InterfaceMoldModelVariationResource::getRouteBaseName($panelId) . '.*')),
                    NavigationItem::make('Teslim Şeklini Yönet')
                        ->url(fn (): string => InterfaceDeliveryMethodVariationResource::getUrl(panel: $panelId))
                        ->icon('heroicon-o-truck')
                        ->isActiveWhen(fn (): bool => request()->routeIs(InterfaceDeliveryMethodVariationResource::getRouteBaseName($panelId) . '.*')),
                    NavigationItem::make('Ürün Özelleştirme')
                        ->url(fn (): string => ManageProductCustomization::getUrl(panel: $panelId))
                        ->icon('heroicon-o-table-cells')
                        ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.pages.product-customization', 'filament.admin.pages.size-dimension-multipliers')),
                ]),
            NavigationItem::make('Arayüz Yönetimi')
                ->group('E-Ticaret')
                ->icon('heroicon-o-paint-brush')
                ->url(fn (): string => BannerSlideResource::getUrl(panel: $panelId))
                ->sort(4)
                ->isActiveWhen(fn (): bool => request()->routeIs(BannerSlideResource::getRouteBaseName($panelId) . '.*')
                    || request()->routeIs(FooterMenuGroupResource::getRouteBaseName($panelId) . '.*')
                    || request()->routeIs(FooterSettingResource::getRouteBaseName($panelId) . '.*')
                    || request()->routeIs(LegalPageResource::getRouteBaseName($panelId) . '.*'))
                ->childItems([
                    NavigationItem::make('Banner Slaytlar')
                        ->url(fn (): string => BannerSlideResource::getUrl(panel: $panelId))
                        ->icon('heroicon-o-photo')
                        ->isActiveWhen(fn (): bool => request()->routeIs(BannerSlideResource::getRouteBaseName($panelId) . '.*')),
                    NavigationItem::make('Footer Menü')
                        ->url(fn (): string => FooterMenuGroupResource::getUrl(panel: $panelId))
                        ->icon('heroicon-o-squares-2x2')
                        ->isActiveWhen(fn (): bool => request()->routeIs(FooterMenuGroupResource::getRouteBaseName($panelId) . '.*')),
                    NavigationItem::make('Footer Ayarları')
                        ->url(fn (): string => FooterSettingResource::getUrl(panel: $panelId))
                        ->icon('heroicon-o-cog-6-tooth')
                        ->isActiveWhen(fn (): bool => request()->routeIs(FooterSettingResource::getRouteBaseName($panelId) . '.*')),
                    NavigationItem::make('Sayfalar')
                        ->url(fn (): string => LegalPageResource::getUrl(panel: $panelId))
                        ->icon('heroicon-o-document-text')
                        ->isActiveWhen(fn (): bool => request()->routeIs(LegalPageResource::getRouteBaseName($panelId) . '.*')),
                ]),
        ];
    }
}
