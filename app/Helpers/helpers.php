<?php

use Illuminate\Support\Carbon;

/**
 * @param $timestamp
 * @param $utc
 * @return int
 */
function timezone_calculate ($timestamp, $utc)
{
    return Carbon::createFromTimestamp($timestamp, $utc)->offsetHours;
}
