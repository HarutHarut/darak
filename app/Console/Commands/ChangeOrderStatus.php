<?php

namespace App\Console\Commands;

use App\Luglocker\Email\EmailCreator;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ChangeOrderStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:change_order_status';

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
        $current_date = Carbon::now('UTC');
        $orders = Order::query()
            ->where('status', 'pending')
            ->with('business')
            ->get();
        $new_current_date = $current_date->subHour(1)->toDateTimeString();
        foreach ($orders as $order) {

            if ($order->business) {
                if ($order->business->timezone) {
                    $new_current_date = $current_date->timezone($order->business->timezone)->subHour(1)->toDateTimeString();
                }
            }
            $arrSizeCount = $order->bookings()
                ->select([DB::raw("count('locker_id') as count"), 'booking_number', 'locker_id'])
                ->with(['order', 'locker.size'])
                ->groupBy('locker_id')
                ->get();
            if ($order->check_in <= $new_current_date) {

                $order->status = 'completed';
                $order->save();


                $viewData = [
                    'subject' => __('general.emails.AddFeedback.subject'),
                    'email' => $order->user->email,
                    'order' => $order,
                    'branch' => $order->bookings[0]->branch,
                    'sizeArr' => $arrSizeCount,
                    'bookingCount' => count($order->bookings),
                ];
                EmailCreator::create(
                    $order->user_id,
                    $order->user->email,
                    $viewData['subject'],
                    view('emails.AddFeedback', $viewData)->render(),
                    'emails.AddFeedback',
                    config('constants.email_type.book_cancel_by_user')
                );
            }
        }

        return Command::SUCCESS;
    }
}
