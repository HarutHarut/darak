<?php

namespace App\Luglocker\Updaters;
use App\Models\Price;

trait PriceUpdater
{
    public function priceUpdate(Price $price, array $data): void
    {
        if (isset($data['range_start'])){
            $price->range_start = $data['range_start'];
        }

        if (isset($data['range_end'])){
            $price->range_end = $data['range_end'];
        }

        if (isset($data['price'])){
            $price->price = $data['price'];
        }

        $price->save();
    }
}
