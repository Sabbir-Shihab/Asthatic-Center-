<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    protected $guarded = [];
    protected $casts = ['skin_types' => 'array', 'is_featured' => 'boolean', 'is_active' => 'boolean'];
    public function brand(): BelongsTo { return $this->belongsTo(Brand::class); }
    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    public function getImageAttribute(?string $value): ?string { return $value ? media_url($value) : $value; }
}
