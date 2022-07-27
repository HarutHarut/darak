<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use \Throwable;
use App\Models\Price;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Admin\Price\{Store, Update};
use App\Http\Controllers\ApiController;

class PriceController extends ApiController
{

    public function index(Request $request): JsonResponse
    {
        $admin = $request->user();
        try {
            $prices = Price::query()
                ->paginate(config('constants.pagination.perPage'));
            return $this->success(200, $prices);
        } catch (Exception $e) {
            $this->errorLog($request, $e, 'Admin/PriceController index action', $admin->id);
            return $this->error(400, 'Something went wrong');
        }
    }

    public function store(Store $request): JsonResponse
    {
        $data = $request->validated();
        $admin = $request->user();

        try {
            DB::beginTransaction();

            foreach ($data['price'] as $value) {
                Price::query()->create([
                    'locker_id' => $data['locker_id'],
                    'range_start' => $value['range_start'],
                    'range_end' => $value['range_end'],
                    'price' => $value['price'],
                ]);
            }

            DB::commit();
            return $this->success(200, [], 'Price created successfully.');
        } catch (Exception $e) {
            DB::rollback();
            return $this->error($e->getCode(), $e->getMessage());
        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, 'Admin/PriceController create action', $admin->id);
            return $this->error(400, 'Price create failed.');
        }
    }

    public function update(Update $request): JsonResponse
    {
        $data = $request->validated();
        $admin = $request->user();

        try {
            DB::beginTransaction();

            foreach ($data['price'] as $value) {
                Price::query()
                    ->where('locker_id', '=', $data['locker_id'])
                    ->find($value['id'])
                    ->update($value);
            }
            DB::commit();
            return $this->success(200, [], 'Price updated successfully.');
        } catch (Exception $e) {
            DB::rollback();
            return $this->error($e->getCode(), $e->getMessage());
        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, 'Admin/PriceController update action', $admin->id);
            return $this->error(400, 'Price update failed.');
        }
    }
}
