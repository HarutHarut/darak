<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Locker;
use App\Models\OpeningTime;
use App\Models\SpecialClosingTime;
use App\Repositories\BranchRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use stdClass;

class BranchService
{
    protected $branchRepository;

    public function __construct(BranchRepository $branchRepository) {
       $this->branchRepository = $branchRepository;
    }

    public function getSetSlug($name): string
    {
        $slug = Str::slug($name);
        $rows = Branch::whereRaw("slug REGEXP '^{$slug}(-[0-9]*)?$'")->get();
        $count = count($rows) + 1;
        return ($count > 1) ? "{$slug}-{$count}" : $slug;
    }

    public function getOpenHour($branch_id, $data) {
        $lockers = Locker::query()->where('branch_id', $branch_id)->get();
        $lockersArray = [];
        $begin = strtotime(date('y-m-d', strtotime( $data['dateRange']['startDate'] )));
        $end = strtotime(date('y-m-d', strtotime( $data['dateRange']['endDate'] )));
        foreach ($lockers as $locker) {
            $lockersGraph = [];
            for ($i = $begin; $i < $end; $i = $i + 86400) {
                $weekDay = Carbon::parse($i)->dayOfWeek;
                $now = Carbon::parse($i)->format("Y-m-d");
                $openingTime = OpeningTime::query()
                    ->where('branch_id', $branch_id)
                    ->where('weekday', -($weekDay-7))
                    ->first();
                $specialClosingTime = SpecialClosingTime::query()->select('special_closing_times.*')
                    ->where('currentDay', $now)
                    ->where('lockers.id', '=', $locker->id)
                    ->join('lockers', 'lockers.id', '=', 'special_closing_times.locker_id')
                    ->join('branches', 'branches.id', '=','lockers.branch_id' )
                    ->get();

                if(isset($openingTime)){

                    $lockerGraph = $this->branchRepository->getLockerDayGraph($locker, $openingTime, $specialClosingTime, $i);
                }else{
                    $lockerGraph = [];
                }
//                return $lockerGraph;

                if($i == $begin) {
                    $lockersGraph = $lockerGraph;
                    $lockerInfo = new stdClass();
                    $lockerInfo->name = $locker->size->name;
                    $lockerInfo->id = $locker->id;
                    $lockersGraph['locker_info'] = $lockerInfo;
                }
                else  {
                    $lockersGraph = array_merge($lockersGraph + $lockerGraph);
                }
            }
            $lockersArray['lockers'][] = $lockersGraph;

        }
        return $lockersArray;
    }

    public function getBranchId($data, $businessId) {
        if (isset($data['branch_id'])) {
           return $data['branch_id'];
        } else {
            $branch = Branch::query()
                ->select('id')
                ->where('business_id', $businessId)
                ->where('status', 1)
                ->with('lockers','openingTimes', 'closingTimes')
                ->first();
            return $branch->id;
        }
    }
}
