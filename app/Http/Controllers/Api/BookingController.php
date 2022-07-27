<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\Book\Cancel;

use App\Luglocker\Email\EmailCreator;
use App\Models\Order;
use App\Notifications\BaseNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use \Throwable;
use Carbon\Carbon;
use Mockery\Exception;
use App\Models\Locker;
use App\Models\Booking;
use App\Models\ClosingTime;
use App\Models\OpeningTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Api\Book\Check;
use App\Http\Requests\Api\Book\Create;
use App\Http\Controllers\ApiController;
use App\Luglocker\Updaters\BookingUpdater;
use App\Luglocker\Price\BookPriceCalculator;

class BookingController extends ApiController
{
    use BookPriceCalculator, BookingUpdater;

    public function book(Create $request): JsonResponse
    {
        $data = $request->validated();
        $user = $request->user();

        try {
            DB::beginTransaction();

            /**
             * @var $locker Locker
             */

            $locker = Locker::query()
                ->with(['price' => function ($query) {
                    $query->select(
                        "id",
                        "locker_id",
                        "range_start",
                        "range_end",
                        "price"
                    )->orderBy('range_start');
                }])
                ->find($data['locker_id']);

            $checkOpeningTime = $this->checkOpeningTimes($locker->branch_id, $data['start'], $data['end']);

            if ($checkOpeningTime) {
                throw new Exception('This branch is closed.', 403);
            }

            $checkOpeningTime = $this->checkClosingTimes($locker->branch_id, $data['start'], $data['end']);

            if ($checkOpeningTime) {
                throw new Exception('This branch is closed.', 403);
            }

            $checkCanBook = Booking::query()
                ->bookedLockersCount($locker->id, $data['start'], $data['end']);

            $availableLockerCount = $locker->count - $checkCanBook;

            if (!$availableLockerCount > 0) {

                throw new Exception('The locker is not available.', 400);
            }

            $price = $this->calculatePrice($locker, $data['start'], $data['end']);

            $booking = Booking::query()
                ->create([
                    'booker_id' => $user->id,
                    'branch_id' => $locker->branch_id,
                    'locker_id' => $data['locker_id'],
                    'start' => $data['start'],
                    'end' => $data['end'],
                    'amount' => $price['total'],
                ])->fresh();

            DB::commit();

            return $this->success(200, ["booking" => $booking], "Book created successfully.");
        } catch (Exception $e) {
            DB::rollback();
            return $this->error($e->getCode(), $e->getMessage());
        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, $user->id, 'BookingController book action');
            return $this->error(400, "Book create failed.");
        }
    }

    public function calculateForBusiness(Request $request): JsonResponse
    {
        $data = $request->all();
        $user = $request->user();

        try {

            /**
             * @var $locker Locker
             */
            $locker = Locker::query()
                ->with(['prices' => function ($query) {
                    $query->select(
                        "id",
                        "locker_id",
                        "range_start",
                        "range_end",
                        "price"
                    )->orderBy('range_start');
                }])
                ->find($data['locker_id']);


            $price = $this->calculatePriceWithHours($locker, $data['time']);

            return $this->success(200, ["price" => $price]);
        } catch (Exception $e) {
            return $this->error($e->getCode(), $e->getMessage());
        } catch (\Throwable $e) {
            $this->errorLog($request, $e, $user->id, 'BookingController check action');
            return $this->error(400, "Book check failed.");
        }
    }

    public function check(Check $request): JsonResponse
    {
        $data = $request->validated();
        $user = $request->user();

        try {

            $start = $data['start'];
            $end = $data['end'];

            /**
             * @var $locker Locker
             */
            $locker = Locker::query()
                ->with(['prices' => function ($query) {
                    $query->select(
                        "id",
                        "locker_id",
                        "range_start",
                        "range_end",
                        "price"
                    )->orderBy('range_start');
                }])
                ->find($data['locker_id']);

            $checkOpeningTime = $this->checkOpeningTimes($locker->branch_id, $start, $end);

            if ($checkOpeningTime) {
                throw new Exception('This branch is closed.', 403);
            }

            $checkOpeningTime = $this->checkClosingTimes($locker->branch_id, $start, $end);

            if ($checkOpeningTime) {
                throw new Exception('This branch is closed.', 403);
            }

            $checkCanBook = Booking::query()
                ->bookedLockersCount($data['locker_id'], $start, $end);
            $availableLockerCount = $locker->count - $checkCanBook;

            if (!$availableLockerCount > 0) {
                throw new Exception('The locker is not available.', 400);
            }

            $price = $this->calculatePrice($locker, $start, $end);

            return $this->success(200, ["price" => $price]);
        } catch (Exception $e) {
            return $this->error($e->getCode(), $e->getMessage());
        } catch (\Throwable $e) {
            $this->errorLog($request, $e, $user->id, 'BookingController check action');
            return $this->error(400, "Book check failed.");
        }
    }

    public function checkCancelByUser(Cancel $request): JsonResponse
    {
        $data = $request->validated();
        $user = $request->user();

        try {

            /**
             * @var $booking Booking
             */

            $booking = Booking::query()
                ->find($data['book_id']);

            $checkCancelTime = $this->checkUserCancelTime($booking->start);

            if ($checkCancelTime) {

                throw new Exception('Cancel time has passed.', 400);
            }

            return $this->success(200, [], 'You can cancel booking.');
        } catch (Exception $e) {
            return $this->error($e->getCode(), $e->getMessage());
        } catch (\Throwable $e) {
            $this->errorLog($request, $e, $user->id, 'BookingController checkCancelByUser action');
            return $this->error(400, "Book cancel check failed.");
        }
    }

    public function bookCancelByUser(Cancel $request): JsonResponse
    {
        $data = $request->validated();
        $user = $request->user();

        try {

            /**
             * @var $booking Booking
             */

            $booking = Booking::query()
                ->find($data['book_id']);
            $order = Order::query()->where('booking_number', $booking->booking_number)->first();
            $price = $this->currencyChangeFromUser($order->price, $order->currency, $user->currency);

            $checkCancelTime = $this->checkUserCancelTime($booking->start);

            if ($checkCancelTime) {

                $booking = $this->bookingUpdate($booking,
                    [
                        'status' => config('constants.booking_status.canceled_by_booker')
                    ]
                );

            } else {

                throw new Exception('Cancel time has passed.', 400);
            }

            $viewData = [
                'subject' => __('general.emails.BookCanceledByUser.subject'),
                'email' => $user->email,
                'order' => $order,
                'price' => $price,
                'user_currency' => $user->currency,
            ];

            EmailCreator::create(
                $user->id,
                $user->email,
                $viewData['subject'],
                view('emails.BookCanceledByUser', $viewData)->render(),
                'emails.BookCanceledByUser',
                config('constants.email_type.book_cancel_by_user')
            );

//            BookCanceledByUserJob::dispatch($user, $order);

            return $this->success(200, ['booking' => $booking], 'Booking successfully canceled.');
        } catch (Exception $e) {
            return $this->error($e->getCode(), $e->getMessage());
        } catch (\Throwable $e) {
            $this->errorLog($request, $e, $user->id, 'BookingController bookCancelByUser action');
            return $this->error(400, "Book check failed.");
        }
    }

    public function checkCancelByBusiness(Cancel $request): JsonResponse
    {
        $data = $request->validated();
        $user = $request->user();

        try {

            /**
             * @var $booking Booking
             */

            $booking = Booking::query()
                ->find($data['book_id']);

            $checkCancelTime = $this->checkBusinessCancelTime($booking->start);

            if ($checkCancelTime) {

                throw new Exception('Cancel time has passed.', 400);
            }

            return $this->success(200, [], 'You can cancel booking.');
        } catch (Exception $e) {
            return $this->error($e->getCode(), $e->getMessage());
        } catch (\Throwable $e) {
            $this->errorLog($request, $e, $user->id, 'BookingController checkCancelByBusiness action');
            return $this->error(400, "Book cancel check failed.");
        }
    }

    public function bookCancelByBusiness(Cancel $request): JsonResponse
    {
        $data = $request->validated();
        $user = $request->user();

        try {

            /**
             * @var $booking Booking
             */

            $booking = Booking::query()
                ->find($data['book_id']);
            $order = Order::query()->where('booking_number', $booking->booking_number)->first();

            $checkCancelTime = $this->checkBusinessCancelTime($booking->start);

            if ($checkCancelTime) {

                $booking = $this->bookingUpdate($booking,
                    [
                        'status' => config('constants.booking_status.canceled_by_business')
                    ]
                );

            } else {

                throw new Exception('Cancel time has passed.', 400);
            }

            $viewData = [
                'subject' => __('general.emails.BookCanceledByBusinessOwner.subject'),
                'email' => $user->email,
                'order' => $order,
            ];

            EmailCreator::create(
                $user->id,
                $user->email,
                $viewData['subject'],
                view('emails.BookCanceledByBusinessOwner', $viewData)->render(),
                'emails.BookCanceledByBusinessOwner',
                config('constants.email_type.book_cancel_by_business_owner')
            );

            return $this->success(200, ['booking' => $booking], 'Booking successfully canceled.');
        } catch (Exception $e) {
            return $this->error($e->getCode(), $e->getMessage());
        } catch (\Throwable $e) {
            $this->errorLog($request, $e, $user->id, 'BookingController bookCancelByBusiness action');
            return $this->error(400, "Book cancel failed.");
        }
    }

    private function checkOpeningTimes(int $branchId, string $start, string $end): bool
    {
        $start = Carbon::parse($start);
        $end = Carbon::parse($end);

        $checkStart = OpeningTime::query()
            ->checkOpeningStart($branchId, $start);

        $checkEnd = OpeningTime::query()
            ->checkOpeningEnd($branchId, $end);

        return !$checkStart || !$checkEnd;
    }

    private function checkClosingTimes(int $branchId, $start, $end): bool
    {
        $checkStart = ClosingTime::query()
            ->checkClosingStart($branchId, $start);

        $checkEnd = ClosingTime::query()
            ->checkClosingEnd($branchId, $end);

        return !$checkStart || !$checkEnd;
    }

    private function checkUserCancelTime(string $start): bool
    {
        $nowTime = now();

        $startTime = Carbon::createFromFormat('Y-m-d H:i:s', $start);

        $cancelTime = $startTime->addMinutes(config('constants.book_cancel_time.user'));

        return $nowTime->lt($cancelTime);
    }

    private function checkBusinessCancelTime(string $start): bool
    {
        $nowTime = now();

        $startTime = Carbon::createFromFormat('Y-m-d H:i:s', $start);

        $cancelTime = $startTime->addMinutes(config('constants.book_cancel_time.business'));

        return $nowTime->lt($cancelTime);
    }

    public function cancelOrder(Request $request)
    {
        $data = $request->all();
        $user = $request->user();
        $new_date = Carbon::now()->subHour(1)->toDateTimeString();

        try {
            DB::beginTransaction();

            $order = Order::query()->find($data['order_id']);

            $arrSizeCount = $order->bookings()
                ->select([DB::raw("count('locker_id') as count"), 'booking_number', 'locker_id'])
                ->with(['order', 'locker.size'])
                ->groupBy('locker_id')
                ->get();

            if ($user->role['name'] == 'business_owner') {
                if ($order->check_in >= $new_date) {
                    $order->update([
                        'status' => 'canceled',
                    ]);
                    $data = [
                        'subject' =>  __('general.emails.BookCanceledByBusinessOwner.subject'),
                        'email' => $user->email,
                        'order' => $order,
                        'user' => $user,
                        'branch' => $order->bookings[0]->branch,
                        'sizeArr' => $arrSizeCount,
                        'bookingCount' => count($order->bookings),
                    ];
                    $view = 'emails.BookCanceledByBusinessOwner';

                    $this->sendMail($data, $view);

                }
                else {
                    return $this->error(400, "Booking cancel failed.");
                }

            }
            elseif ($user->role['name'] == 'user') {

//                return response()->json([$order->check_in, Carbon::now()->format('Y-m-d H:i:s')]);
                if ($order->check_in >= Carbon::now()) {

                    $order->update([
                        'status' => 'canceled',
                    ]);

                    $data = [
                        'subject' =>  __('general.emails.BookCanceledByUser.subject'),
                        'email' => $user->email,
                        'order' => $order,
                        'user' => $user,
                        'branch' => $order->bookings[0]->branch,
                        'sizeArr' => $arrSizeCount,
                        'bookingCount' => count($order->bookings),
                    ];
                    $view = 'emails.BookCanceledByUserToUser';

                    $this->sendMail($data, $view);

                    $data['email'] = $order->bookings[0]->branch->email;
                    $data['subject'] = __('general.emails.BookCanceledByBranch.subject');
                    $view = 'emails.BookCanceledByUserToBusiness';

                    $this->sendMail($data, $view);


                } else {
                    return $this->error(400, __('general.bookingCancelFailedUser'));
                }
            } elseif ($user->role['name'] == 'admin') {
                $order->update([
                    'status' => 'canceled',
                ]);
            }


            DB::commit();
            return $this->success(200, ['order' => $order], "Booking canceled successfully.");
        } catch (\Throwable $e) {
            DB::rollback();
//            dd($e->getMessage());
            $this->errorLog($request, $e, 'Admin/BookingController cancel action', $user->id);
            return $this->error(400, "Booking cancel failed.");
        }
    }

    public function sendMail($data, $view) {
        $user = $data['user'];
        $viewData = [
            'subject' => $data['subject'],
            'email' => $data['email'],
            'order' => $data['order'],
            'user' => $user,
            'branch' => $data['branch'],
            'sizeArr' => $data['sizeArr'],
            'bookingCount' => $data['bookingCount'],
        ];
//        $viewData = [
//            $data
//        ];

        EmailCreator::create(
            $user->id,
            $data['email'],
            $viewData['subject'],
            view($view, $viewData)->render(),
            $view,
            config('constants.email_type.book_cancel_by_user')
        );
    }

}
