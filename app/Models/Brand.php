<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Brand extends Model
{
    protected $guarded = [];
    public function products(): HasMany { return $this->hasMany(Product::class); }
    public function getLogoAttribute(?string $value): ?string { return $value ? media_url($value) : $value; }
}
