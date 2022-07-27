<?php

namespace App\Repositories;

use App\Models\Order;
use Brick\Math\BigNumber;

class OrderRepository
{

    /**
     * @return object
     */
    public function orders(): object
    {
        return Order::query();
    }

    /**
     * @param $user_id
     * @param $data
     * @return mixed
     */
    public function businessOrder($user_id, $data): mixed
    {
        return $this->orders()
            ->where('businesses.user_id', $user_id)
            ->join('businesses', 'businesses.id', '=', 'orders.business_id')
            ->count();
    }

    /**
     * @return object
     */
    public function getOrders(): object
    {
        return $this->orders()->get();
    }

    /**
     * @return object
     */
    public function ordersCount(): object
    {
        return $this->orders()->count();
    }

    /**
     * @param $data
     * @return mixed
     */
    public function orderByDate($data): mixed
    {
        return $this->orders()->where(function ($query) use ($data) {
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

    /**
     * @param $data
     * @return mixed
     */
    public function adminOrders($data): mixed
    {
        return $this->orders()->count();
    }

    /**
     * @param $count
     * @param $user_id
     * @return mixed
     */
    public function lastOrdersByBusiness($count, $user_id): mixed
    {
        return $this->orders()
            ->select('orders.*')
            ->where('businesses.user_id', $user_id)
            ->join('businesses', 'businesses.id', '=', 'orders.business_id')
            ->with('user')
            ->orderBy('orders.created_at', 'desc')
            ->limit($count)->get();
    }

    /**
     * @param $count
     * @param $user_id
     * @return mixed
     */
    public function lastOrdersByAdmin($count, $user_id): mixed
    {
        return $this->orders()
            ->select('orders.*')
            ->with('user')
            ->orderBy('orders.created_at', 'desc')
            ->limit($count)->get();
    }
}
