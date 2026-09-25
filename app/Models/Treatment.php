<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Treatment extends Model
{
    protected $guarded = [];
    protected $casts = ['is_featured' => 'boolean', 'is_active' => 'boolean'];

    public static function categories(): array
    {
        return [
            'laser-aesthetic' => 'Laser & Aesthetic',
            'hair-scalp' => 'Hair & Scalp',
            'womens-intimate-wellness' => "Women's & Intimate Wellness",
            'skin-dermatology' => 'Skin & Dermatology',
            'ayurveda-panchakarma' => 'Ayurveda & Panchakarma',
            'naturopathy-natural-therapy' => 'Naturopathy & Natural Therapy',
            'holistic-health-lifestyle' => 'Holistic Health & Lifestyle',
            'detox-hijama-wellness' => 'Detox, Hijama & Wellness',
            'facial-beauty' => 'Facial & Beauty',
            'body-aesthetics' => 'Body Aesthetics',
        ];
    }

    public static function legacyCategoryMap(): array
    {
        return [
            'aesthetic-treatments' => 'laser-aesthetic',
            'wellness-ayurveda' => 'ayurveda-panchakarma',
            'consultation' => 'holistic-health-lifestyle',
            'skin-analysis' => 'skin-dermatology',
            'premium-packages' => 'facial-beauty',
        ];
    }

    public function categoryLabel(): string
    {
        return self::categories()[$this->category] ?? self::categories()[self::legacyCategoryMap()[$this->category] ?? 'laser-aesthetic'];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getImageAttribute(?string $value): ?string
    {
        return $value ? media_url($value) : $value;
    }
}
