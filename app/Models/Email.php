<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_to_id
 * @property string $from_email
 * @property string $to_email
 * @property string $cc
 * @property string $bcc
 * @property string $subject
 * @property string $content
 * @property string $priority
 * @property int $attempts
 * @property string $template_name
 * @property int $status
 * @property string $schedule_date
 * @property string $date_sent
 * @property string $email_type
 */

class Email extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_to_id',
        'from_email',
        'to_email',
        'cc',
        'bcc',
        'subject',
        'content',
        'priority',
        'attempts',
        'template_name',
        'status',
        'schedule_date',
        'date_sent',
        'email_type',
        'attachment'
    ];
}
