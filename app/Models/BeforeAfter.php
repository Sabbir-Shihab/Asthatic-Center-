<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BeforeAfter extends Model { protected $guarded = []; protected $casts = ['is_published' => 'boolean']; public function getBeforeImageAttribute(?string $value): ?string { return $value ? media_url($value) : $value; } public function getAfterImageAttribute(?string $value): ?string { return $value ? media_url($value) : $value; } }
