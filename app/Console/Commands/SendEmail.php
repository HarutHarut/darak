<?php

namespace App\Console\Commands;

use App\Models\Email;
use Illuminate\Console\Command;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;
use \Throwable;

class SendEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $emails = Email::query()
            ->where('status', '=', config('constants.email_status.is_not_sent'))
            ->where('attempts', '<', 3)
            ->get();

        if (count($emails)) {
            foreach ($emails as $email) {
                $schedule_date = date('Y-m-d h:i:s');
                try {
                    Mail::send([], [], function (Message $message) use ($email) {
                        $message->to($email->to_email)
                            ->subject($email->subject)
                            ->setBody($email->content, 'text/html');

                        if ($email->attachment){
                            $message->attach($email->attachment);
                        }
                    });
                    $email->date_sent = date('Y-m-d h:i:s');
                    $email->status = config('constants.email_status.sent');
                } catch (\Throwable $e){

                }
                $email->attempts++;
                $email->schedule_date = $schedule_date;
                $email->save();
            }
        }
    }
}
