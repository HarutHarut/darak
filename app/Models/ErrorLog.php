<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property string $category
 * @property string $level
 * @property string $user_ip
 * @property string $request_url
 * @property string $message
 * @property string $server_ip
 * @property string $request_id
 */

class ErrorLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category',
        'level',
        'user_ip',
        'request_url',
        'message',
        'server_ip',
        'request_id'
    ];
}
