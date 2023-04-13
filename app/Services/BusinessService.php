<?php

namespace App\Services;



class BusinessService
{
    /**
     * @param $timezone
     * @return int|mixed|string
     */
    public function timezoneUTC($timezone)
    {
        $tz = file_get_contents(public_path() . '/timezones.json' );
        $tz = json_decode($tz, true);

        $offset = 0;
        foreach ($tz as $t){
            foreach ($t['utc'] as $location){
                if($location == $timezone){
                    $offset = $t['offset'];
                    break;
                }
            }
            if ($offset)
                break;
        }

        if($offset > 0){
            $offset = '+' . $offset;
        }

        return $offset;
    }

    public static function timezoneOptions()
    {
        $tz = file_get_contents(public_path() . '/timezones.json');
        $tz = json_decode($tz, true);
        $options = [];
        foreach ($tz as $t) {
            foreach ($t['utc'] as $item) {
                $options[] = (object) ['value' => $item, 'text' => $item];
            }
        }
        return $options;
    }
}
