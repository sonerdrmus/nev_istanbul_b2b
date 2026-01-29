<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class CategoriesCluster extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-folder';

    protected static ?string $navigationGroup = 'E-Ticaret';

    protected static ?string $navigationLabel = 'Kategoriler';

    protected static ?string $clusterBreadcrumb = 'Kategoriler';

    public static function getNavigationSort(): ?int
    {
        return 2;
    }
}
