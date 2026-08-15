<?php

namespace App\Models\Concerns;

use App\Support\CatalogLabelTranslator;
use Illuminate\Support\Facades\Schema;

trait FillsLocalizedNameFromCatalog
{
    /**
     * Canonical TR field that matching/sync still uses.
     */
    protected static function localizedNameSourceAttribute(): string
    {
        return 'name';
    }

    protected static function bootFillsLocalizedNameFromCatalog(): void
    {
        static::saving(function ($model): void {
            $sourceAttr = $model::localizedNameSourceAttribute();
            $source = trim((string) ($model->{$sourceAttr} ?? ''));
            if ($source === '') {
                return;
            }

            $enAttr = $sourceAttr.'_en';
            $itAttr = $sourceAttr.'_it';
            if (! Schema::hasColumn($model->getTable(), $enAttr)) {
                return;
            }

            $pair = CatalogLabelTranslator::fillPair(
                $source,
                $model->{$enAttr} ?? null,
                $model->{$itAttr} ?? null,
            );
            if (blank($model->{$enAttr})) {
                $model->{$enAttr} = $pair['en'] !== '' ? $pair['en'] : null;
            }
            if (blank($model->{$itAttr})) {
                $model->{$itAttr} = $pair['it'] !== '' ? $pair['it'] : null;
            }
        });
    }
}
