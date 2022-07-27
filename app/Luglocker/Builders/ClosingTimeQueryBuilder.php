<?php


namespace App\Luglocker\Builders;


use Illuminate\Database\Eloquent\Builder;

class ClosingTimeQueryBuilder extends Builder
{
    public function checkClosingStart($branchId, $start): int
    {
        return $this
            ->where('branch_id', '=', $branchId)
            ->where('start', '<', $start)
            ->where('end', '>', $start)
            ->count();
    }

    public function checkClosingEnd($branchId, $end): int
    {
        return $this
            ->where('branch_id', '=', $branchId)
            ->where('start', '<', $end)
            ->where('end', '>', $end)
            ->count();
    }
}
