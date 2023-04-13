<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Book\Cancel;
use App\Http\Requests\Admin\Book\Update;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use \Throwable;
use App\Models\{Booking};
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\ApiController;

class BookingController extends ApiController
{

    public function index(Request $request): JsonResponse
    {
        $admin = $request->user();
        try {
            $bookings = Booking::query()
                ->with(['branch.business','locker'])
                ->orderByDesc('created_at')
                ->paginate(config('constants.pagination.perPage'));

            return $this->success(200, ['bookings' => $bookings]);
        } catch (\Throwable $e) {
            $this->errorLog($request, $e, 'Admin/BookingController index action', $admin->id);
            return $this->error(400, 'Could not get bookings');
        }
    }

    public function update(Update $request, int $id): JsonResponse
    {
        $data = $request->validated();
        $admin = $request->user();

        try {
            DB::beginTransaction();

            $booking = Booking::query()->find($id);
            $booking->update($data);

            DB::commit();
            return $this->success(200, ['booking' => $booking], "Booking updated successfully.");
        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, 'Admin/BookingController update action', $admin->id);
            return $this->error(400, "Booking update failed.");
        }
    }

    public function cancel(Cancel $request): JsonResponse
    {
        $data = $request->validated();
        $admin = $request->user();

        try {
            DB::beginTransaction();

            $booking = Booking::query()->find($data['id']);
            $booking->update([
                'status' => config('constants.booking_status.canceled_by_admin')
            ]);

            DB::commit();
            return $this->success(200, ['booking' => $booking], "Booking canceled successfully.");
        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, 'Admin/BookingController cancel action', $admin->id);
            return $this->error(400, "Booking cancel failed.");
        }
    }

    public function show(Request $request, $id): JsonResponse
    {
        $admin = $request->user();

        try {
            $booking = Booking::query()
                ->with([
                    'branch.business',
                    'locker.size',
                    'user'
                ])->find($id);

            return $this->success(200, ['booking' => $booking]);

        } catch (\Throwable $e) {
            $this->errorLog($request, $e, 'Admin/BookingController show action',$admin->id);
            return $this->error(400, $e->getMessage());
        }
    }
}
