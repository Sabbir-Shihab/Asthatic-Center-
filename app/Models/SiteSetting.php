<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SiteSetting extends Model
{
    protected $guarded = [];

    public function getValueAttribute(?string $value): ?string
    {
        if (! in_array($this->key, ['hero_banner', 'site_logo', 'site_favicon', 'about_image', 'wellness_image'], true)) {
            return $value;
        }

        return $value ? media_url($value) : $value;
    }
}
