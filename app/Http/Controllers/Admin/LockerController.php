<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Exception;
use \Throwable;
use App\Models\Locker;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Admin\Locker\Store;
use App\Http\Requests\Admin\Locker\Update;
use App\Http\Controllers\ApiController;

class LockerController extends ApiController
{

    public function index(Request $request): JsonResponse
    {
        $admin = $request->user();
        try {
            $lockers = Locker::query()->paginate(config('constants.pagination.perPage'));
            return $this->success(200, $lockers);
        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, 'Admin/LockerController index action', $admin->id);
            return $this->error(400, 'Could not get the lockers');
        }
    }

    public function store(Store $request): JsonResponse
    {
        $data = $request->validated();
        $admin = $request->user();

        try {
            DB::beginTransaction();

            $locker = Locker::query()->create($data);

            DB::commit();

            return $this->success(200, ['locker' => $locker], 'Locker created successfully.');
        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, 'Admin/LockerController create action', $admin->id);
            return $this->error(400, 'Locker create failed.');
        }
    }


    public function update(Update $request, $id): JsonResponse
    {
        $data = $request->validated();
        $admin = $request->user();

        try {
            DB::beginTransaction();

            $locker = Locker::query()->find($id);
            $locker->update($data);

            DB::commit();

            return $this->success(200, ['locker' => $locker], 'Locker updated successfully.');
        } catch (Exception $e) {
            DB::rollback();
            return $this->error($e->getCode(), $e->getMessage());
        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, 'Admin/LockerController update action', $admin->id);
            return $this->error(400, 'Locker update failed.');
        }
    }
}
