<?php


namespace App\Luglocker\Builders;


use Illuminate\Database\Eloquent\Builder;

class OpeningTimeQueryBuilder extends Builder
{
    public function checkOpeningStart($brunchId, $start): int
    {
        return $this
            ->where('branch_id', '=', $brunchId)
            ->where('weekday', '=', $start->dayOfWeek)
            ->where('start', '<', $start->toTimeString())
            ->where('end', '>', $start->toTimeString())
            ->count();
    }

    public function checkOpeningEnd($brunchId, $end): int
    {
        return $this
            ->where('branch_id', '=', $brunchId)
            ->where('weekday', '=', $end->dayOfWeek)
            ->where('start', '<', $end->toTimeString())
            ->where('end', '>', $end->toTimeString())
            ->count();
    }
}
