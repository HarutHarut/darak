<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Invoices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getAll(Request $request)
    {
        $data = $request->all();

        $user = $request->user();
        $createDates = [];
        $business = Business::where('user_id', $user->id)
            ->with('invoices')
            ->first();
        foreach ($business->invoices as $item) {
            $createDates[] = $item->created_at->format('Y');
        }
        $createDates = array_unique($createDates);

        $filterYear = [];
        foreach ($business->invoices as $item) {
            if (isset($data['year']) && $data['year'] !== null) {
                if ($item->created_at->format('Y') == $data['year']) {
                    $filterYear[] = $item->id;
                }
            }
            elseif($item->created_at->format('Y') == max($createDates)){
                $filterYear[] = $item->id;
            }

        }
        $invoices = $business->invoices()->whereIn('id', $filterYear)->get();

        return response()->json(['business' => $business, 'filterDate' => $createDates, 'invoices' => $invoices]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function changeStatus(Request $request)
    {
        $data = $request->all();
        $invoice = Invoices::find($data['invoice_id']);
        $invoice->status = 1;
        $invoice->save();

        return response()->json($invoice);
    }

    public function datas()
    {
        $datas['years'] = [];
        for($i = 2020; $i <= now()->year; $i++){
            $datas['years'][] = $i;
        }
        $datas['months'] = config('constants.months');

        return response()->json($datas);
    }

}
