<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use Exception;
use \Throwable;
use App\Models\ClosingTime;
use App\Models\OpeningTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Admin\OpenTime\Store as OpenTimeStore;
use App\Http\Requests\Admin\OpenTime\Update as OpenTimeUpdate;
use App\Http\Requests\Admin\CloseTime\Store as CloseTimeStore;
use App\Http\Requests\Admin\CloseTime\Update as CloseTimeUpdate;


class TimeController extends ApiController
{
    public function storeOpenTime(OpenTimeStore $request): JsonResponse
    {
        $data = $request->validated();
        $admin = $request->user();

        try {
            DB::beginTransaction();

            foreach ($data['time'] as $value) {
                OpeningTime::query()->create([
                    'branch_id' => $value['branch_id'],
                    'weekday' => $value['weekday'],
                    'start' => $value['start'],
                    'end' => $value['end'],
                    'status' => $value['status'],
                ]);
            }

            DB::commit();

            return $this->success(200, [], 'Open time created successfully.');
        } catch (Exception $e) {
            DB::rollback();
            return $this->error($e->getCode(), $e->getMessage());
        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, 'Admin/TimeController createOpenTime action', $admin->id);
            return $this->error(400, 'Open time create failed.');
        }
    }

    public function updateOpenTime(OpenTimeUpdate $request): JsonResponse
    {
        $data = $request->validated();
        $admin = $request->user();

        try {
            DB::beginTransaction();

            foreach ($data['time'] as $value) {
                OpeningTime::query()->find($value['id'])->update($value);
            }

            DB::commit();
            return $this->success(200, [], 'Open time updated successfully.');
        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, 'Admin/TimeController updateOpenTime action', $admin->id);
            return $this->error(400, 'Open time update failed.');
        }
    }



    public function storeCloseTime(CloseTimeStore $request): JsonResponse
    {
        $data = $request->validated();
        $admin = $request->user();

        try {
            DB::beginTransaction();

            foreach ($data['time'] as $value) {
                ClosingTime::query()->create([
                    'branch_id' => $value['branch_id'],
                    'start' => $value['start'],
                    'end' => $value['end'],
                ]);
            }

            DB::commit();

            return $this->success(200, [], 'Close time created successfully.');
        } catch (Exception $e) {
            DB::rollback();
            return $this->error($e->getCode(), $e->getMessage());
        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, 'Admin/TimeController storeCloseTime action', $admin->id);
            return $this->error(400, 'Close time create failed.');
        }
    }

    public function updateCloseTime(CloseTimeUpdate $request): JsonResponse
    {
        $data = $request->validated();
        $admin = $request->user();

        try {
            DB::beginTransaction();

            foreach ($data['time'] as $value) {
                ClosingTime::query()->find($value['id'])->update($value);
            }

            DB::commit();
            return $this->success(200, [], 'Close time updated successfully.');
        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, 'Admin/TimeController updateCloseTime action', $admin->id);
            return $this->error(400, 'Close time update failed.');
        }
    }

}
