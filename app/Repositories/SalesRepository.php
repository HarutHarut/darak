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
        return $this->sales()->sum('price');
    }

    public function businessSalesSum($user_id,$data) {
        return $this->sales()
            ->where('businesses.user_id', $user_id)
            ->join('businesses', 'businesses.id', '=', 'orders.business_id')
            ->sum('price');
    }

}
