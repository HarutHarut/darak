<?php

namespace App\Http\Controllers\Api;

use App\Luglocker\General;
use App\Luglocker\Price\BookPriceCalculator;
use App\Luglocker\Price\BranchCalculate;
use App\Models\Branch;
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
                ->whereHas('branches',function ($q) {
                    $q->where('status', '1');
                    $q->where('working_status', '1');
                })
                ->where('name', '!=', 'null')
                ->orderBy('name', 'asc')
                ->get();

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
                ->limit(8)
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

    /**
     * @param Request $request
     * @param $slug
     * @return JsonResponse
     */
    public function getCity(Request $request, $slug)
    {
        $user_currency = General::resolveCurrency($request);

        $city = City::with(['branches' => function ($q) {
            $q->where('status', 1);
            $q->where('working_status', 1);
            $q->orderBy('is_bookable', 'desc');
        }])
            ->where('slug', $slug)
            ->first();

        try {
            if(!$city){
                return $this->success(200, ['status' => 404],"City doesn't exist.");
            }
            if (isset($city->branches)) {
                foreach ($city->branches as $recommendedBranch) {
                    $business_currency = $branch->business['currency'] ?? 'EUR';
                    if (isset($recommendedBranch->lockers)) {
                        $branchMinPrice = BranchCalculate::minPrice($recommendedBranch->lockers);

                        $recommendedBranch['min_price'] = BookPriceCalculator::currencyChangeFromUser($branchMinPrice, $business_currency, $user_currency, false) . ' ' . $user_currency;
                    } else {
                        $recommendedBranch['min_price'] = '';
                    }

                    $recommendedBranch['average_rating'] = BranchCalculate::averageRating($recommendedBranch->feedbacks);
                    $recommendedBranch['average_rating_double'] = round($recommendedBranch['average_rating']);

                    unset($recommendedBranch['feedbacks']);
                    unset($recommendedBranch['description']);
                }
            }

            $city['branches'] = [];
            return $this->success(200, [
                'city' => $city
            ]);
        } catch (\Throwable $e) {
            $this->errorLog($request, $e, 'Api/CityController getCity action');
            return $this->error(400, 'Could not get city.');
        }

    }

    /**
     * @return JsonResponse
     */
    public function getCities(): JsonResponse
    {
        try {
            $cities = City::all();

            return $this->success(200, ['cities' => $cities]);
        } catch (\Throwable $e) {
            return $this->error(400, 'Could not get city.');
        }

    }

    public function logo(Request $request) {

        $city = City::select('logo')->where('name', $request->get('name'))->first();
        return response()->json(['logo' => $city['logo'] ?? ''],200);

    }
}
