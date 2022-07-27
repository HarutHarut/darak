<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
/**
 * Class Contact
 * @package App\Models
 * @property  integer $id
 * @property  string $name
 * @property  string $phone
 * @property  string $address
 * @property  string $message
 */
class Contact extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'last_name',
        'email',
        'phone',
        'address',
        'message'
    ];
}
