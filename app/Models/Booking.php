<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'kapster_id',
        'service_id',
        'customer_name',
        'customer_phone',
        'booking_date',
        'booking_time',
        'status',
        'notes',
    ];

    protected $casts = [
        'booking_date' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function kapster()
    {
        return $this->belongsTo(Kapster::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
