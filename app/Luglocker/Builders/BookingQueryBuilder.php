<?php


namespace App\Luglocker\Builders;


use Illuminate\Database\Eloquent\Builder;

class BookingQueryBuilder extends Builder
{
    public function bookedLockersCount($id,$start,$end): int
    {
        return $this->where('locker_id','=',$id)
        ->where(function ($q) use ($start, $end){
            $q->whereBetween('start', [$start, $end])
                ->orWhereBetween('end', [$start, $end]);
        })->count();
    }
}
