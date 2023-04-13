<?php

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Database\Query\Builder;

class SalesRepository {

    public function sales(): mixed
    {
        return Order::query();
    }

    public function salesByDate($data): mixed
    {
        return $this->sales()->where(function ($query) use ($data) {
            $query
                ->where(function ($q) use ($data) {
                    $q->where('check_in', '<=', $data['startDate'])
                        ->where('check_out', '>=', $data['startDate']);
                })
                ->orWhere(function ($q) use ($data) {
                    $q->where('check_in', '<=', $data['endDate'])
                        ->where('check_out', '>=', $data['endDate']);
                })
                ->orWhere(function ($q) use ($data) {
                    $q->where('check_in', '>=', $data['startDate'])
                        ->where('check_out', '<=', $data['endDate']);
                });
        });
    }

    public function adminSalesSum($data) {
        return $this->sales()
            ->where('status', 'completed')
//            ->sum('price');
            ->count();
    }

    public function businessSalesSum($user_id, $data)
    {
        return $this->sales()
            ->with('business')
            ->where('status', 'completed')
            ->whereHas('business', function ($q) use ($user_id) {
                $q->where('user_id', $user_id);
            })
//            ->join('businesses', 'businesses.id', '=', 'orders.business_id')
//            ->sum('price');
            ->count();

    }

}
