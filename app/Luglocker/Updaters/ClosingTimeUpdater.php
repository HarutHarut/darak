<?php


namespace App\Luglocker\Updaters;


use App\Models\ClosingTime;

trait ClosingTimeUpdater
{
    public function closingTimeUpdate(ClosingTime $closeTime, array $data): void
    {
        if (isset($data['start'])) {
            $closeTime->start = $data['start'];
        }

        if (isset($data['end'])) {
            $closeTime->end = $data['end'];
        }

        $closeTime->save();
    }
}
