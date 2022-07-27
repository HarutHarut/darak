<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoices extends Model
{
    use HasFactory;

    public $fillable = [
        'business_id',
        'month',
        'amount',
        'amount_currency',
        'business_amount',
        'business_currency',
        'status',
        'file_name'
    ];
    const PAY_ATTEMPTS = 30;

    public function order(){
        return $this->hasMany(Order::class);
    }

    public function transactions(){
        return $this->hasMany(Transaction::class);
    }

    public function business(){
        return $this->hasOne(Business::class,'id','business_id');
    }
}
