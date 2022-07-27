<?php

namespace App\Http\Controllers\Api;

use \Throwable;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\ApiController;

class CityController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        try {
            $cities = City::query()
                ->paginate(config('constants.pagination.perPage'));
//                ->get();

            return $this->success(200, [
                'cities' => $cities
            ]);
        } catch (\Throwable $e) {
            $this->errorLog($request, $e, 'Api/CityController index action');
            return $this->error(400, 'Could not get cities.');
        }
    }

    public function topCities(Request $request): JsonResponse
    {
        try {
            $cities = City::query()
                ->where('top', 1)
                ->limit(4)
                ->get();

            return $this->success(200, [
                'cities' => $cities
            ]);
        } catch (\Throwable $e) {
            $this->errorLog($request, $e, 'Api/CityController topCities action');
            return $this->error(400, 'Could not get top cities.');
        }
    }

    public function show(Request $request, $id): JsonResponse
    {
        try {

            $city = City::query()
                ->find($id);

            return $this->success(200, [
                'city' => $city
            ]);
        } catch (\Throwable $e) {
            $this->errorLog($request, $e, 'Api/CityController show action');
            return $this->error(400, 'Could not get city.');
        }
    }

    public function logo(Request $request) {

        $city = City::select('logo')->where('name', $request->get('name'))->first();
        return response()->json(['logo' => $city['logo']],200);

    }
}
