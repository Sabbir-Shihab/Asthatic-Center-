<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $guarded = [];
    protected $casts = ['appointment_at' => 'datetime'];

    public function treatment()
    {
        return $this->belongsTo(Treatment::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function focusArea()
    {
        return $this->belongsTo(FocusArea::class);
    }
}
