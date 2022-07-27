<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangeBusinessStatus;
use App\Jobs\BusinessBlockedJob;
use App\Jobs\UserBlockedJob;
use App\Luglocker\Email\EmailCreator;
use App\Models\Booking;
use App\Models\Branch;
use App\Models\Business;
use App\Models\Locker;
use App\Models\Order;
use App\Models\Settings;
use App\Models\Size;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ApiController;
use App\Luglocker\Price\BookPriceCalculator;

class OrderController extends ApiController
{
    use BookPriceCalculator;

    public function getOrders(Request $request)
    {
        $user = $request->user();
        $user_currency = $user->currency ?? "EUR";
        $data = $request->all();
        $orders = Order::with('bookings.branch', 'business', 'user', 'feedback');

        try {
            if (isset($data['check_in']) && isset($data['check_out'])) {
                $orders = $orders
                    ->where('check_in', '>=', $data['check_in'])
                    ->where('check_out', '<=', $data['check_out']);
            }

            if (isset($data['status'])) {
                if($data['status'] !== 'all'){
                    $orders = $orders->where('status', $data['status']);
                }
            }

            if ($user->role['name'] == 'admin') {
                if (isset($data['user_id'])) {
                    $orders = $orders->where('user_id', $data['user_id']);
                }
                if (isset($data['business_id'])) {
                    $orders = $orders->where('business_id', $data['business_id']);
                }

                if (isset($data['branch_id'])) {

                    $orders = $orders->whereHas('bookings', function ($q) use ($data) {
                        $q->where('branch_id', $data['branch_id']);
                    });
                }
//            } elseif ($user->role['name'] == 'business_owner' && $data['type'] == 'business_owner')
            } elseif ($user->role['name'] == 'business_owner')
            {
                $orders = $orders->where('business_id', $user->business['id']);
                if (isset($data['branch_id'])) {
                    $orders = $orders->whereHas('bookings', function ($q) use ($data) {
                        $q->where('branch_id', $data['branch_id']);
                    });
                }
            } elseif ($user->role['name'] == 'user' || ($user->role['name'] == 'business_owner' && (!isset($data['type']) || $data['type'] !== 'business'))) {
                $orders = $orders->where('user_id', $user->id);
            }

            $orders = $orders->orderBy('created_at', 'desc');
            $orders = $orders->paginate(config('constants.pagination.perPage'));

            foreach ($orders as $order){
                $order['priceCurrency'] = $this->currencyChangeFromUser($order->price, $order->currency, $user_currency, false) . ' ' . $user_currency;
            }
//return response()->json($orders);

            $currency_courses = Settings::query()->where('key', 'currency')->first();

            return $this->success(200, ['orders' => $orders, 'currency_courses' => json_decode($currency_courses->value)]);

        } catch (\Throwable $e) {
            $this->errorLog($request, $e, 'OrderController all action', $user->id);
            return $this->error(400, "Order all failed.");
        }
    }

    public function orderSingle(Request $request, $book_number): JsonResponse
    {
        $user = $request->user();
        try {
            $order = Order::query()
                ->with(['user', 'bookings.branch', 'bookings.locker.size', 'feedback'])
                ->where('booking_number', $book_number);

            if ($user->isBusiness() || $user->isUser()){
                $order = $order->where(function($q) use ($user) {
                    if ($user->isBusiness()){
                        $q->where('business_id', $user->business->id);
                    } else{
                        $q->where('user_id', $user->id);
                    }
                });
            }
            $order = $order->first();

            $arrSizeCount = $order->bookings()
                ->select([DB::raw("count('locker_id') as count"), 'booking_number', 'locker_id'])
                ->with(['order', 'locker.size'])
                ->groupBy('locker_id')
                ->get();
            $order['arrSizeCount'] = $arrSizeCount;


            if ($user->role['name'] == 'user') {
                $order['price'] = $this->currencyChangeFromUser($order['price'], $order['currency'], $user->currency ?? "EUR", '', $format = true);
                $order['currency'] = $user->currency ?? "EUR";
            }

            return $this->success(200, ['order' => $order]);

        } catch (\Throwable $e) {
            $this->errorLog($request, $e, 'OrderController all action', $user->id);
            return $this->error(400, $e->getMessage());
        }
    }

    public function blockedBusinessOrUserStatus(ChangeBusinessStatus $request)
    {
        $data = $request->validated();
        $admin = $request->user();

        try {
            DB::beginTransaction();

            if (isset($data['business_id'])) {
                $business = Business::query()->find($data['business_id']);
                $data['status'] = config('constants.business_status.blocked');
                $business->status = $data['status'];
                $business->save();

                $businessOwner = $business->user;

                $viewData = [
                    'subject' => __('general.emails.BusinessBlocked.subject'),
                    'email' => $businessOwner->email
                ];

                EmailCreator::create(
                    $businessOwner->id,
                    $businessOwner->email,
                    $viewData['subject'],
                    view('emails.BusinessBlocked', $viewData)->render(),
                    'emails.BusinessBlocked',
                    config('constants.email_type.business_block')
                );

                DB::commit();
                return $this->success(200, ['business' => $business], "Business blocked successfully");
            }

            if (isset($data['user_id'])) {
                $user = User::query()->find($data['user_id']);
                $data['status'] = config('constants.user_status.blocked');
                $user->status = $data['status'];
                $user->save();

                $viewData = [
                    'subject' =>  __('general.emails.UserBlocked.subject'),
                    'email' => $user->email
                ];

                EmailCreator::create(
                    $user->id,
                    $user->email,
                    $viewData['subject'],
                    view('emails.UserBlocked', $viewData)->render(),
                    'emails.UserBlocked',
                    config('constants.email_type.user_block')
                );

                DB::commit();
                return $this->success(200, ['user' => $user], "User blocked successfully");
            }

        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, 'Admin/BusinessController changeStatus action', $admin->id);
            return $this->error(400, $e->getMessage());
        }
    }

    public function search(Request $request)
    {
        $data = $request->all();
        $orders = Booking::query()->with(['branch.business', 'locker'])->where('start', '>=', $data['check_in'])
            ->where('end', '<=', $data['check_out']);

        if (isset($data['business_id'])) {
            $orders->whereHas('branch', function ($q) use ($data) {
                $q->where('business_id', $data['business_id']);
            });
        }
        if (isset($data['search_text'])) {
            $orders->where('booking_number', 'like', "%" . $data['search_text'] . "%");
        }
        return response()->json($orders->paginate(config('constants.pagination.perPage')));
    }
}
