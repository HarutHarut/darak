<?php

namespace App\Services;

class OrdersService
{
    public function byBusiness($query) {
        return $query->where('business', $data['business_id']);
    }
}
