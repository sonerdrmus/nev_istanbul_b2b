<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class ProductsCluster extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'E-Ticaret';

    protected static ?string $navigationLabel = 'Ürünler';

    protected static ?string $clusterBreadcrumb = 'Ürünler';

    public static function getNavigationSort(): ?int
    {
        return 1;
    }
}
