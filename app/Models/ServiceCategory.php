<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServiceCategory extends Model
{
    protected $guarded = [];
    protected $casts = [
        'focus_areas' => 'array',
        'is_active' => 'boolean',
    ];

    public function focusAreas(): HasMany
    {
        return $this->hasMany(FocusArea::class)->orderBy('sort_order')->orderBy('name');
    }

    public static function publishedList()
    {
        return once(fn () => static::query()
            ->where('is_active', true)
            ->with(['focusAreas' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order')->orderBy('name')])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get());
    }

    public function getImageAttribute(?string $value): ?string
    {
        return $value ? media_url($value) : $value;
    }
}