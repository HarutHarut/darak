<?php

namespace App\Console\Commands;

use App\Jobs\InvoiceJob;
use App\Luglocker\Email\EmailCreator;
use App\Luglocker\Price\BookPriceCalculator;
use App\Models\Business;
use App\Models\Invoices;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade as PDF;

class Invoice extends Command
{
    use BookPriceCalculator;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:invoice';

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
        $now = Carbon::now();
        $first_day_last_mount = date('Y-m-d', strtotime('first day of previous month'));
        $last_day_last_mount = date('Y-m-d', strtotime('last day of previous month'));
        $mount_number = $now->subMonth()->format('m');
        $today = Carbon::now()->format('m_Y');
        $lastMount = Carbon::now();
        $daysToAdd = config('app.admin.due_by');
        $due_by = $lastMount->addDays($daysToAdd)->toDateString();

//        1. select the orders without invoces
        $orders = Order::selectRaw("sum(price) as sum, business_id, currency")   // sum(price) as sum,
        ->where('invoice_status', '!=', 1)
            ->where('check_in', '>=', $first_day_last_mount . ' 00:00:00')
            ->where('check_in', '<=', $last_day_last_mount . ' 23:59:59')
            ->groupBy('business_id')
            ->get();

        foreach ($orders as $order) {
            try {

//        2. change
                $amount = $this->currencyChangeFromUser($order->sum, $order->currency, config('app.admin.currency'), config('app.admin.currency'), false);
                $file_name = Str::uuid();
                $business = Business::find($order->business_id);

//        3. Invoice:create
                $invoice = new Invoices();
                $invoice->business_id = $order->business_id;
                $invoice->month = $mount_number;
                $invoice->period_first_day = $first_day_last_mount;
                $invoice->period_last_day = $last_day_last_mount;
                $invoice->amount = $this->calculatedSiteFee($amount);
                $invoice->amount_currency = config('app.admin.currency');
                $invoice->business_amount = $order->sum;
                $invoice->business_currency = $order->currency ?? 'EUR';
                $invoice->status = 0;
                $invoice->file_name = '';
                $invoice->save();

                $invoice->invoice_number = $invoice->id + 10000;
                $invoice->save();
                $pdf = PDF::loadView('emails.invoice', [
                    'order' => $order,
                    'amount' => $amount,
                    'business' => $business,
                    'invoice_number' => $invoice->invoice_number,
                    'first_day_last_mount' => $first_day_last_mount,
                    'due_by' => $due_by,
                    'last_day_last_mount' => $last_day_last_mount
                ]);
                $path = 'invoices/' . $today . '/' . $order->business_id;
                Storage::makeDirectory($path);
                $pdf->save(storage_path('app/public/') . $path . '/' . $file_name . '.pdf');

//        5. update invoice with pdf file path
                $invoice->file_name = $path . '/' . $file_name . '.pdf';
                $invoice->due_by = $due_by;
                $invoice->save();

//       6. update order about invoice created
                Order::where('business_id', $order->business_id)->update([
                    'invoice_status' => 1
                ]);
                $businessUser = $invoice->business()->first()->user()->first();

                $viewData = [
                    'subject' => __('general.emails.invoice.subject', ['date' => Carbon::now()->subMonth()->format('Y-m')]),
                    'invoiceFile' => storage_path('app/public/' . $invoice->file_name),
                    'businessUser' => $businessUser
                ];

                EmailCreator::create(
                    $businessUser->id,
                    $businessUser->email,
                    $viewData['subject'],
                    view('emails.InvoiceView', $viewData)->render(),
                    'emails.InvoiceView',
                    config('constants.email_type.book_business_owner'),
                    null,
                    $viewData['invoiceFile']
                );
            } catch (\Throwable $e) {
                echo $e->getMessage();
            }
        }
        return 0;
    }

    public function calculatedSiteFee($amount)
    {
        $amount = $amount * config('app.admin.commission') / 100;
        return (float)number_format((float)$amount, 2, '.', '');
    }
}
