<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use App\Luglocker\Media\MediaActions;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
        $search = $request->input('search');
        $admin = $request->user();
        try {
            $cities = City::query()
//                ->where('top', 1)
                ->with('media');

            if(isset($search) && !empty($search)){
                $cities = $cities->where(function($q) use ($search){
                    $q->where('name', 'like', '%' . $search . '%');
                });
            }
            $cities = $cities->paginate(config('constants.pagination.perPage'));

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
            $meta = array('en' => null, 'ru' => null, 'ch' => null, 'am' => null, 'fr' => null);
            $city['meta_title'] = $city['meta_title'] ?? $meta;
            $city['meta_description' ]= $city['meta_description'] ?? $meta;
            $city['meta_keywords'] = $city['meta_keywords'] ?? $meta;

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
            $meta = array('en' => null, 'ru' => null, 'ch' => null, 'am' => null, 'fr' => null);

            $data['logo'] = env('APP_URL') . Storage::url(Storage::putFile('cities/logo', $data['image']));
            if(isset($data['preview']) && $data['preview'] !== null){
                $data['preview'] = env('APP_URL') . Storage::url(Storage::putFile('cities/banner', $data['preview']));
            }
            $data['meta_title'] = $data['meta_title'] ?? $meta;
            $data['meta_description' ]= $data['meta_description'] ?? $meta;
            $data['meta_keywords'] = $data['meta_keywords'] ?? $meta;
            $slug = Str::slug($data['name']);
            $rows = City::whereRaw("slug REGEXP '^{$slug}(-[0-9]*)?$'")->get();
            $count = count($rows) + 1;
            if ($count > 1) {
                $slug = "{$slug}-{$count}";
            }
            $data['slug'] = $slug;
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

            if(isset($data['image']) && $data['image'] !== 'null') {
                if ($city->logo) {
                    Storage::delete(str_replace(env('APP_URL') . '/storage/', '', $city->logo));
                }

                $city['logo'] = env('APP_URL') . Storage::url(Storage::putFile('cities/logo', $data['image']));
                $city->save();
                $data['logo'] = $city['logo'];

//                $city->update([
//                    'logo' => env('APP_URL') . Storage::url(Storage::putFile('cities/logo', $data['image']))
//                ]);
            }

            if(isset($data['banner'])) {
                if ($city->preview) {
                    Storage::delete(str_replace(env('APP_URL') . '/storage/', '', $city->preview));
                }
                $city['preview'] = env('APP_URL') . Storage::url(Storage::putFile('cities/banner', $data['banner']));
                $city->save();
                $data['preview'] = $city['preview'];
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
