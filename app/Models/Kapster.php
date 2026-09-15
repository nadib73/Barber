<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kapster extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'photo_url',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function schedules()
    {
        return $this->hasMany(KapsterSchedule::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
