<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Testimonial extends Model { protected $guarded = []; protected $casts = ['is_featured' => 'boolean', 'is_approved' => 'boolean']; public function getClientPhotoAttribute(?string $value): ?string { return $value ? media_url($value) : $value; } }
