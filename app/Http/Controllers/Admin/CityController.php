<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use App\Luglocker\Media\MediaActions;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Admin\City\{Show, Store, Update};
use App\Models\City;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use \Throwable;

class CityController extends ApiController
{
    use MediaActions;

    public function index(Request $request): JsonResponse
    {
//        return response()->json($request->get('perPage'));
        $admin = $request->user();
        try {
            $cities = City::query()
//                ->where('top', 1)
                ->with('media')
                ->paginate(config('constants.pagination.perPage'));
            return $this->success(200, ['cities' => $cities]);
        } catch (\Throwable $e) {
            $this->errorLog($request, $e, 'Admin/CityController index action', $admin->id);
            return $this->error(400, 'Could not get cities');
        }
    }


    public function show(Show $request, int $id): JsonResponse
    {
        $admin = $request->user();

        try {
            $city = City::with('media')->find($id);

            return $this->success(200, ['city' => $city]);
        } catch (\Throwable $e) {
            $this->errorLog($request, $e, 'Admin/CityController show action', $admin->id);
            return $this->error(400, $e->getMessage());
        }
    }

    public function store(Store $request): JsonResponse
    {
        $data = $request->validated();
        $admin = $request->user();

        try {
            DB::beginTransaction();

            $data['logo'] = env('APP_URL') . Storage::url(Storage::putFile('cities/logo', $data['image']));
            $city = City::query()->create($data);

            DB::commit();
            return $this->success(201, ['city' => $city], "City created successfully.");
        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, 'Admin/CityController store action', $admin->id);
            return $this->error(400, "City create failed.");
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $data = $request->all();
        $admin = $request->user();

        try {
            DB::beginTransaction();

            $city = City::find($id);

            if(isset($data['image'])) {
                if ($city->logo) {
                    Storage::delete(str_replace(env('APP_URL') . '/storage/', '', $city->logo));
                }
                $city->update([
                    'logo' => env('APP_URL') . Storage::url(Storage::putFile('cities/logo', $data['image']))
                ]);
            }
            $city->update($data);

            DB::commit();
            return $this->success(200, ['city' => $data], "City updated successfully.");
        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, 'Admin/CityController update action', $admin->id);
            return $this->error(400, "City update failed.");
        }
    }
}
