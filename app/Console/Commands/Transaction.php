<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\PaymentController;

class Transaction extends Command
{
    public $PAYMENT_STATUSES = [
        'default' => 0,
        'success' => 1,
        'pending' => 2,
        'error' => 3,
        'decline' => 4,
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:transaction';

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
        $transactions = \App\Models\Transaction::query()->where('status', $this->PAYMENT_STATUSES['pending'])->get();
        $username = config('services.bank.username');
        $password = config('services.bank.password');
        $url = config('services.bank.url');
        foreach ($transactions as $transaction){

            $getOrderStatus =  $url . "getOrderStatusExtended.do?userName=$username&password=$password&language=en&orderId=" . $transaction['transaction_id'];
            $response = json_decode(file_get_contents($getOrderStatus));
            if ((isset($response->errorCode) && $response->errorCode > 0) || (isset($response->OrderStatus) &&  $response->OrderStatus > 2)) {
                if(isset($response->OrderStatus)){
                    $transaction->message = "OrderStatus: " .$response->OrderStatus  . " / actionCodeDescription: " . $response->actionCodeDescription;
                }elseif (isset($response->errorCode)){
                    $transaction->message = "errorCode: " .$response->errorCode  . " / errorMessage: " . $response->errorMessage;
                }
                $transaction->status = $this->PAYMENT_STATUSES['decline'];
                $transaction->save();

            } else {
                if(isset($response->orderStatus) && $response->orderStatus == 2){
                    $transaction->message = "OrderStatus: " .$response->orderStatus  . " / actionCodeDescription: " . $response->actionCodeDescription;
                    $transaction->status = $this->PAYMENT_STATUSES['success'];
                    $transaction->invoice->status = $this->PAYMENT_STATUSES['success'];
                    $transaction->invoice->save();
                }elseif(isset($response->errorCode)){
                    $transaction->message = "orderStatus: " .$response->orderStatus  . " / errorMessage: " . $response->actionCodeDescription;
                }
                $transaction->save();
            }

        }

        return Command::SUCCESS;
    }
}
