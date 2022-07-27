<?php

namespace App\Http\Controllers\Api;

use App\Models\Currency;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\ApiController;


class CurrencyController extends ApiController
{

    public function all(): JsonResponse
    {
        $currency = Currency::query()
            ->get();

        return $this->success(200, ['currency' => $currency]);
    }
}
