<?php

namespace App\Luglocker\Price;

use App\Models\Branch;
use App\Models\Email;
use App\Models\Feedback;
use App\Models\Locker;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BranchCalculate
{
    public static function minPrice($lockers)
    {
        $min_prices = [];
        foreach ($lockers as $locker) {
            if (isset($locker->price_per_hour) && $locker->price_per_hour !== null){
                $min_prices[] = $locker->price_per_hour;
            }

            if (isset($locker->price_per_day) && $locker->price_per_day !== null){
                $min_prices[] = $locker->price_per_day;
            }

            if ($locker->prices->min('price') && $locker->prices->min('price') !== null){
                $min_prices[] = $locker->prices->min('price');
            }
        }

        return (count($lockers) && count($min_prices)) ? min($min_prices) : '';
    }

    public static function avgPrice($lockers)
    {
        $avg_prices = [];
        foreach ($lockers as $locker){
            $avg_prices[] = round($locker->prices->avg('price'), 1);
        }

        return count($avg_prices) > 1 ? min($avg_prices) : $avg_prices;
    }

    public static function averageRating($feedbacks)
    {
        return round($feedbacks->avg('rating'));
    }

    public static function averageRatingDouble($feedbacks)
    {
        return round($feedbacks->avg('rating'), 2);
    }

    public static function recommendedBranch($branch, $isBookable = 1, $limit = 3)
    {
        $city_id = $branch->pluck('city_id');
        $address = $branch->pluck('address');
        $business_id = $branch->pluck('business_id');

        $recommended = Branch::with(['feedbacks', 'city'])
            ->where('city_id', $branch->city_id)
            ->where('status', 1)
            ->where('id', '!=', $branch->id)
            ->select('*', DB::raw("6371 * acos(cos(radians(" . $branch->lat . "))
                * cos(radians(branches.lat))
                * cos(radians(branches.lng) - radians(" . $branch->lng . "))
                + sin(radians(" .$branch->lat. "))
                * sin(radians(branches.lat))) AS distance"))
            ->where('working_status', 1)
            ->where('is_bookable', $isBookable)
            ->oldest('distance')
            ->limit($limit)
            ->get();

        return $recommended;
    }
}
