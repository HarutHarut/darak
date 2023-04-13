<?php

namespace App\Http\Controllers\Api;

use App\Luglocker\Price\BranchCalculate;
use App\Models\Branch;
use App\Models\Business;
use App\Models\Feedback;
use App\Models\SpecialClosingTime;
use App\Services\BranchService;
use App\Services\ImageService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Mockery\Exception;
use App\Models\Locker;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\Locker\Create;
use App\Http\Requests\Api\Locker\Update;
use App\Luglocker\Updaters\LockerUpdater;

class LockerController extends ApiController
{
    use LockerUpdater;

    protected $imageService;
    protected $branchService;

    public function __construct(ImageService $imageService, BranchService $branchService)
    {
        $this->imageService = $imageService;
        $this->branchService = $branchService;
    }

    public function all(Request $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->all();

        try {

            $lockers = Locker::query()
                ->where('branch_id', '=', $data['branch_id'])
                ->paginate(config('constants.pagination.perPage'));

            return $this->success(200, $lockers);

        } catch (\Throwable $e) {
            $this->errorLog($request, $e, $user->id, 'LockerController all action');
            return $this->error(400, "Locker all failed.");
        }
    }

    public function create(Create $request): JsonResponse
    {
        $data = $request->validated();
        $user = $request->user();

        try {
            DB::beginTransaction();

            $check_locker = Locker::query()->where('branch_id', $data['branch_id'])->where('size_id', $data['size_id'])->get();
            if (!count($check_locker)) {
                $locker = Locker::query()->create([
                    'branch_id' => $data['branch_id'],
                    'size_id' => $data['size_id'],
                    'name' => null, // No need for this version
                    'count' => $data['count'],
                    'price_per_hour' => $data['price_per_hour'] ?? null,
                    'price_per_day' => $data['price_per_day'] ?? null,
                ]);
                if (count($data['prices']) && isset($data['prices'][0]['range_start']) && isset($data['prices'][0]['range_end']) && isset($data['prices'][0]['price'])) {
                    $locker->prices()->createMany($data['prices']);
                }
            } else {
                return $this->error(400, '', ['error' => __('general.locker.add.repeatSize')]);
            }

            DB::commit();

            return $this->success(200, ['locker' => $locker], "Locker created successfully.");
        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, 'LockerController create action', $user->id);
            return $this->error(400, "Locker create failed.");
        }
    }

    public function update(Update $request): JsonResponse
    {
        $data = $request->validated();
        $user = $request->user();

        try {
            DB::beginTransaction();

            $locker = Locker::query()
                ->find($data['id']);

            if ($locker == null) {
                throw new Exception('Locker not found.', 404);
            }

            $locker->update([
                'size_id' => $data['size_id'],
                'price_per_day' => $data['price_per_day'],
                'price_per_hour' => $data['price_per_hour'],
                'count' => $data['count'],
            ]);
            $locker->prices()->delete();
            if (count($data['prices']) && $data['prices'][0]['price'] !== null && $data['prices'][0]['range_end'] !== null && $data['prices'][0]['range_start'] !== null) {
                $locker->prices()->createMany($data['prices']);
            }
            DB::commit();

            return $this->success(200, [], "Locker updated successfully.");
        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, $user->id, 'LockerController update action');
            return $this->error(400, "Locker update failed.");
        }
    }

    public function show(Request $request, $id): JsonResponse
    {
        $user = $request->user();

        try {

            $locker = Locker::query()
                ->with(['prices', 'size'])
                ->find($id);
            $locker['currency'] = $locker->branch->business->currency ?? 'EUR';

            return $this->success(200, ['locker' => $locker]);
        } catch (\Throwable $e) {
            $this->errorLog($request, $e, 'LockerController show action', $user->id);
            return $this->error(400, "Locker show failed.");
        }
    }

    public function lockersMap(Request $request): JsonResponse
    {
        $data = $request->all();
        return response()->json($data);
    }

    public function updateGraph(Request $request)
    {
        $data = $request->all();
        foreach ($data['selected'] as $cube) {

            $start = explode('/', $cube['value']);
            if (isset($cube['type'])) {
                SpecialClosingTime::query()
                    ->where('locker_id', $cube['lockerId'])
                    ->where('start', $start[1])
                    ->delete();
            } else {
                SpecialClosingTime::create([
                    'start' => $start[1],
                    'locker_id' => $cube['lockerId'],
                    'currentDay' => $start[0],
                    'created_at' => Carbon::now()
                ]);
            }
        }
        $user = Auth::user();
        $business = Business::query()->where('user_id', $user->id)->first();
        $branchId = $this->branchService->getBranchId($data, $business->id);
        $openingTime = $this->branchService->getOpenHour($branchId, $data);
        return response()->json($openingTime);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function closeDaysWithDateRange(Request $request)
    {
        $data = $request->all();
        $start = strtotime($data['dateRange']['startDate']);
        $end = strtotime($data['dateRange']['endDate']);
        for ($j = 0; $j < count($data['selectedLocker']); $j++) {
            if ($data['chosenAction'] == 'close') {
                for ($i = $start; $i < $end; $i = $i + 3600) {
                    $date = Carbon::parse($i)->format('Y-m-d');
                    $time = Carbon::parse($i)->format('H:i:s');
                    SpecialClosingTime::query()->firstOrCreate([
                        "start" => $time,
                        "locker_id" => $data['selectedLocker'][$j],
                        "currentDay" => $date,
                    ], [
                        "created_at" => Carbon::now()
                    ]);
                }
            } else {
                for ($i = $start; $i < $end; $i = $i + 3600) {
                    $date = Carbon::parse($i)->format('Y-m-d');
                    $time = Carbon::parse($i)->format('H:i:s');
                    SpecialClosingTime::query()
                        ->where('start', '=', $time)
                        ->where('currentDay', '=', $date)
                        ->where("locker_id", $data['selectedLocker'][$j])
                        ->delete();
                }
            }
        }
        $user = Auth::user();
        $business = Business::query()->where('user_id', $user->id)->first();
        $branchId = $this->branchService->getBranchId($data, $business->id);
        $openingTime = $this->branchService->getOpenHour($branchId, $data);
        return response()->json($openingTime);

    }

    public function removeLocker(Request $request)
    {
        $data = $request->all();
        $locker = Locker::find($data['locker_id']);

        if (count($locker->bookings)) {
            return response()->json('false', 422);
        } else {
            if (count($locker->prices)) {
                foreach ($locker->prices as $item) {
                    $item->delete();
                }
            }
            $locker->delete();
            return response()->json('delete');
        }


    }
}
