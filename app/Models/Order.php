<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'business_id',
        'user_id',
        'booking_number',
        'check_in',
        'check_out',
        'price',
        'currency',
        'status',
        'pay_type'
    ];

    public function bookings(){
        return $this->hasMany(Booking::class, 'booking_number', 'booking_number');
    }

    public function business(){
        return $this->belongsTo(Business::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function feedback(){
        return $this->belongsTo(Feedback::class, 'id', 'order_id');
    }
}
