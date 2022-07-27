<?php

namespace App\Luglocker\Email;

use App\Models\Email;
use Illuminate\Database\Eloquent\Model;

class EmailCreator
{
    public static function create($userToId, $toEmail, $subject, $content,  $templateName, $emailType, $fromEmail = null, $attachment = null) : Model
    {
        return Email::query()->create([
            'user_to_id' => $userToId,
            'to_email' => $toEmail,
            'subject' => $subject,
            'content' => $content,
            'template_name' => $templateName,
            'email_type' => $emailType,
            'from_email' => $fromEmail ?? config('mail.default_mail'),
            'attachment' => $attachment,
        ]);
    }

}
