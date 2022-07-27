<?php

namespace App\Luglocker\Updaters;

use App\Models\Business;
use Illuminate\Support\Facades\Storage;

trait BusinessUpdater
{
    public function businessUpdate(Business $business, array $data, $logo): Business
    {
        if (isset($data['name'])){
            $business->name = $data['name'];
        }

        if (isset($data['email'])){
            $business->email = $data['email'];
        }

        if (isset($data['lat'])){
            $business->lat = $data['lat'];
        }

        if (isset($data['lng'])){
            $business->lng = $data['lng'];
        }

        if (isset($data['phone'])){
            $business->country_code = json_decode($business['country_code']);
            $business->country_code->country = $data['phone_country'];
            $business->country_code->code = $data['phone_code'];
            $business->country_code = json_encode($business->country_code);
            $business->phone = $data['phone'];
        }

        if (isset($data['address'])){
            $business->address = $data['address'];
        }

        if (isset($data['status'])){
            $business->status = $data['status'];
        }

        if (isset($data['publish'])){
            $business->publish = $data['publish'];
        }

        if (isset($data['currency'])){
            $business->currency = $data['currency'] ?? 'EUR';
        }

        if (isset($data['logo']) && gettype($data['logo']) !== 'string') {
            if ($business->logo) {
                $new_path = str_replace(env('APP_URL') . '/storage/', '', $business->logo);
                Storage::delete($new_path);
            }

            $business->logo = env('APP_URL') . Storage::url(Storage::putFile('business/logo', $data['logo']));
        }

        $business->save();

        return $business->fresh();
    }
}
