<?php

namespace App\Models\Concerns;

use App\Support\LocaleContent;

trait HasLocalizedName
{
    public function getLocalizedNameAttribute(): string
    {
        return LocaleContent::display(
            $this->name ?? null,
            $this->name_en ?? null,
            $this->name_it ?? null,
        );
    }
}
