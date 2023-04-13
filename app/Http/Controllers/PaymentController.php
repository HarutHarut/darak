<?php

namespace App\Http\Controllers;

use App\Models\Invoices;
use App\Models\Order;
use App\Models\Transaction;
use App\Services\OrdersService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends ApiController
{
    public $PAYMENT_STATUSES = [
        'default' => 0,
        'success' => 1,
        'pending' => 2,
        'error' => 3,
        'decline' => 4,
    ];
    /**
     * @var OrdersService
     */
    private $ordersService;

    /**
     * PaymentController constructor.
     * @param OrdersService $ordersService
     */
    public function __construct(OrdersService $ordersService)
    {
        $this->ordersService = $ordersService;
    }


    public function pay($invoice_id)
    {
        $invoice = Invoices::query()->find($invoice_id);
        if (!$invoice) {
            return redirect()->back();
        }

//        dd($invoice->transactions);

        $transaction = new Transaction();
        $transaction->invoice_id = $invoice_id;
        $transaction->save();

        //userName=29535026_api&password=LockersX123&amount=100&currency=051&description=X_soft_Order_1000011&orderNumber=4&returnUrl=http://luglockers.xsoft.com/

//        $amount = $invoice->amount;
        $amount = 1;

        $description = "Bank_s_fees_will_be_charged_from_the_customers";

        $req_url = config('services.bank.url') . 'register.do?';
        $req_url .= "userName=" . config('services.bank.username');
        $req_url .= "&password=" . config('services.bank.password');
        $req_url .= "&amount=" . number_format($amount, 2, '.', '') * 100;
        $req_url .= "&currency=" . '051';
        $req_url .= "&description=" . $description;
//        $req_url .= "&language=en";
//        $req_url .= "&orderNumber=" . (1000 + $invoice_id + $transaction->id);
        $req_url .= "&orderNumber=" . $transaction->id;
        $req_url .= "&returnUrl=" . route('payment.callback');

        $payment_response = json_decode(file_get_contents($req_url), true);
//        return response()->json(['pay' => $payment_response]);
//        dd($payment_response);
        if (isset($payment_response['formUrl'])) {
            $transaction->status = $this->PAYMENT_STATUSES['pending'];
            $transaction->transaction_id = $payment_response['orderId'];
            $transaction->save();
//            dd($payment_response['formUrl']);
            return redirect($payment_response['formUrl']);
        } else {
            if (isset($payment_response['errorMessage'])) {
                $transaction->status = $this->PAYMENT_STATUSES['error'];
                $transaction->message = $payment_response['errorMessage'];
                $transaction->save();
                return response()->json(['message' => $payment_response['errorMessage']]);
            }
        }
    }

    public function paymentCallback()
    {
        if (isset($_GET['paymentID'])) {
            $order_id = $_GET['paymentID'];
            $transaction = Transaction::where('transaction_id', $order_id)->orderBy("id", "DESC")->first();
            if (!$transaction) {
                $message = 'transaction is not found';
                return redirect()->route('/')->with(['error' => $message]);
            }

            $transaction->invoice->pay_attempts = $transaction->invoice->pay_attempts + 1;
            $transaction->invoice->save();
            if($transaction->invoice->pay_attempts <= Invoices::PAY_ATTEMPTS){
            $response = $this->checkTransaction($order_id);
                if ((isset($response->errorCode) && $response->errorCode > 0) || (isset($response->OrderStatus) &&  $response->OrderStatus > 2)) {
                    $transaction->invoice->status = 1;
                    if(isset($response->OrderStatus)){
                        $transaction->message = "OrderStatus: " .$response->OrderStatus  . " / actionCodeDescription: " . $response->actionCodeDescription;
                    }elseif (isset($response->errorCode)){
                        $transaction->message = "errorCode: " .$response->errorCode  . " / errorMessage: " . $response->errorMessage;
                    }
                    $transaction->status = $this->PAYMENT_STATUSES['decline'];
                    $transaction->save();

                } else {
                    if(isset($response->OrderStatus) && $response->OrderStatus == 2){
                        $transaction->invoice->status = 1;
                        $transaction->message = "OrderStatus: " .$response->OrderStatus  . " / actionCodeDescription: " . $response->actionCodeDescription;
                        $transaction->status = $this->PAYMENT_STATUSES['success'];
                    }else{
                        $transaction->invoice->status = 2;
                        $transaction->message = "orderStatus: " .$response->orderStatus  . " / errorMessage: " . $response->actionCodeDescription;
                    }
                    $transaction->save();
                }
                $transaction->invoice->save();
                header("Location: " . env('APP_FRONT_URL') . 'office/invoices');
                exit();
            }
        } else {
            $message = 'order is not found';
            return redirect()->route('/')->with(['error' => $message]);
        }
    }

    public function checkTransaction($order_id)
    {
        $transaction = Transaction::where('transaction_id', $order_id)->first();

        return $this->ordersService->checkTransaction($transaction['transaction_id']);
    }
    public function newPay(Request $request, $invoice_id) {
        $invoice = Invoices::find($invoice_id);
        if (!$invoice) {
            return redirect()->back();
        }

        $transaction = new Transaction();
        $transaction->invoice_id = $invoice_id;
        $transaction->save();

        try {
            $order = rand(0, 99999);
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL            => config('services.bank.url') . "register.do",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING       => '',
                CURLOPT_MAXREDIRS      => 10,
                CURLOPT_TIMEOUT        => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST  => 'POST',
                CURLOPT_POSTFIELDS     => array('userName'    => config('services.bank.username'),
                                                'password'    => config('services.bank.password'),
                                                'orderNumber' => $order,
                                                'amount'      => number_format($invoice->amount, 2, '.', '') * 100,
                                                'returnUrl'   => env('APP_URL') . '/payment-callback',
                                                'language'    => "en",
                                                'currency'    => '051',
                                                'description'    => 'Invoice-'. $invoice->invoice_number
                ),
            ));

            $response = curl_exec($curl);
            $response = json_decode($response);
            if ($response && $response->errorCode == 0) {
                curl_close($curl);
                $transaction->status = $this->PAYMENT_STATUSES['pending'];
                $transaction->transaction_id = $response->orderId;
                $transaction->save();
                return response()->json($response);
            }
            curl_close($curl);
            return response()->json(["success"=>false], 500);
        } catch (\Throwable $e) {
            $this->errorLog($request, $e, 'PaymentController');
            return response()->json("something went wrong", 500);
        }

    }
}
