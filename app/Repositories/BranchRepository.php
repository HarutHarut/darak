<?php

namespace App\Repositories;


use App\Models\Branch;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use phpDocumentor\Reflection\Types\Collection;

class BranchRepository
{
    protected array $hoursArr = [
        '0' => '00:00:00',
        '1' => '01:00:00',
        '2' => '02:00:00',
        '3' => '03:00:00',
        '4' => '04:00:00',
        '5' => '05:00:00',
        '6' => '06:00:00',
        '7' => '07:00:00',
        '8' => '08:00:00',
        '9' => '09:00:00',
        '10' => '10:00:00',
        '11' => '11:00:00',
        '12' => '12:00:00',
        '13' => '13:00:00',
        '14' => '14:00:00',
        '15' => '15:00:00',
        '16' => '16:00:00',
        '17' => '17:00:00',
        '18' => '18:00:00',
        '19' => '19:00:00',
        '20' => '20:00:00',
        '21' => '21:00:00',
        '22' => '22:00:00',
        '23' => '23:00:00',
    ];

    public function getLockerDayGraph($locker, $openingTime, $specialClosingTime, $date): array
    {
        $openingHours = [];
        $today = Carbon::parse($date)->format("Y-m-d");
        $hoursArr = $this->hoursArr;
        foreach ($hoursArr as $value) {
            if ($value >= $openingTime['start'] && $value <= $openingTime['end']) {
                $openingHours[$today . "/" . $value] = 1;
            } else {
                $openingHours[$today . '/' . $value] = 0;
            }
        }
        if (isset($specialClosingTime)) {
            foreach ($specialClosingTime as $specialHour) {
                if($openingHours[$today . '/' . $specialHour['start']] == 1) {
                    $openingHours[$today . '/' . $specialHour['start']] = 2;
                }
            }
        }
        return $openingHours;
    }

    /**
     * @return Builder
     */
    public function branches(): Builder
    {
        return Branch::query()->with(['city', 'currency', 'lockers', 'business'])->withCount(['lockers', 'feedbacks']);
    }

    /**
     * @param $data
     * @return Builder
     */
    public function branchesByBusinessName($data): Builder
    {
        return $this->branches()->where('business_id', $data['business_id']);
    }

    /**
     * @param $data
     * @param $user_id
     * @return LengthAwarePaginator
     */
    public function businessBranchSearch($data, $user_id): LengthAwarePaginator
    {
        return $this->branches()->where(function ($q) use ($data, $user_id) {
            $q->where('name', 'like', '%' . $data['search'] . '%')
                ->orWhere('address', 'like', '%' . $data['search'] . '%')
                ->orWhere('slug', 'like', '%' . $data['search'] . '%')
                ->orWhereHas('city', function ($query) use ($data) {
                    $query->where('name', 'like', '%' . $data['search'] . '%');
                });
        })->whereHas('business', function ($query) use ($user_id) {
            $query->where('user_id', '=', $user_id);
        })->paginate(config('constants.pagination.perPage'));
    }

    /**
     * @param $data
     * @return LengthAwarePaginator
     */
    public function adminBranchSearch($data): LengthAwarePaginator
    {
        $response = $this->branches();
        if (isset($data['business_id'])) {
            $response->whereHas('business', function ($query) use ($data) {
                $query->where('id', '=', $data['business_id']);
            });
        }
        if(isset($data['search'])) {
            $response = $response->where(function ($q) use ($data) {
                $q->where('name', 'like', '%' . $data['search'] . '%')
                    ->orWhere('address', 'like', '%' . $data['search'] . '%')
                    ->orWhere('slug', 'like', '%' . $data['search'] . '%')
                    ->orWhereHas('city', function ($query) use ($data) {
                        $query->where('name', 'like', '%' . $data['search'] . '%');
                    });
            });
        }
        return $response->paginate(config('constants.pagination.perPage'));
    }

    /**
     * @param $data
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function mapBranchFilter($data)
    {
        $date = Carbon::now();

        $response = Branch::query()->with(['business', 'city']);
        $response = $response->where('status', config('constants.branch_status.verified'))
            ->where('working_status', config('constants.branch_open_status.open'))
            ->whereBetween('lat', [$data['south'], $data['north']])
            ->whereBetween('lng', [$data['west'], $data['east']]);


        if (isset($data['openDayNight']) && $data['openDayNight'] == 'true') {
            $ids = [];
            $branches = $response->get();
            foreach ($branches as $branch) {
                $openDayCount = 0;
                foreach ($branch->openingTimes as $item) {

                    if ($item->start == '00:00:00' && $item->end == '23:59:00' && $item->status == 1) {
                        $openDayCount++;
                    }
                }
                if ($openDayCount == 7) {
                    $ids[] = $branch->id;
                }
            }

            $response = $response->whereIn('id', $ids);
        }


        if (isset($data['openToday']) && $data['openToday'] == 'true') {
            $branches = $response->get();
            $ids = [];
            foreach ($branches as $branch){
                $weekCount = count($branch->openingTimes->where('weekday', $date->dayOfWeekIso)->where('status', 1));

                if($weekCount !== 0){
                    $ids[] = $branch->id;
                }
            }
            $response = $response->whereIn('id', $ids);

        }

        if (isset($data['cardPayment']) && $data['cardPayment'] == 'true') {
            $response = $response->where('card_payment', 1);
        }
        if (isset($data['bestRate']) && $data['bestRate'] == 'true') {
            $response = $response->orderByDesc('avg_rating');
        }

        return $response->get();
    }
}
