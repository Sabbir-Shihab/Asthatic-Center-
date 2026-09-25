<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FocusArea extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    public function localized(string $field): string
    {
        $bangla = $this->{$field.'_bn'};
        if (app()->getLocale() === 'bn' && filled($bangla)) {
            return $bangla;
        }

        $value = (string) ($this->{$field} ?? '');

        return app()->getLocale() === 'bn' ? t($value) : $value;
    }

    public function getImageAttribute(?string $value): ?string
    {
        return $value ? media_url($value) : $value;
    }
}
