<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Doctor extends Model
{
    protected $guarded = [];
    protected $casts = ['credentials' => 'array', 'is_featured' => 'boolean', 'is_active' => 'boolean'];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getPhotoAttribute(?string $value): ?string
    {
        return $value ? media_url($value) : $value;
    }

    public function getHeroPhotoAttribute(?string $value): ?string
    {
        return $value ? media_url($value) : $value;
    }
}
