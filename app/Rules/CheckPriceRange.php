<?php

namespace App\Rules;

use App\Models\Price;
use Illuminate\Contracts\Validation\Rule;

class CheckPriceRange implements Rule
{


    /**
     * CreateTime a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
//        $prices = $this->getAllPrices($value);

        if ($value[0]['range_start'] != 0) {
            return false;
        }

        $prices = $value;


        foreach ($prices as $key => $price) {

            if (isset($prices[$key + 1]) && !empty($value[$key + 1])) {

                if ($price['range_end'] != $prices[$key + 1]['range_start']) {
                    return false;
                }

                if ($price['range_end'] == $prices[$key + 1]['range_end']) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'The price ranges is invalid.';
    }


    public function getAllPrices($value)
    {
        $savedPrices = Price::query()
            ->select('range_start', 'range_end')
            ->where('locker_id', "=", $this->locker_id)
            ->get();

        $savedPrices = $savedPrices->toArray();

        return array_merge($savedPrices, $value);
    }

    public function sortPrices($prices)
    {
        usort($prices, function ($a, $b) {
            return $a['range_start'] > $b['range_start'];
        });

        return $prices;
    }
}
