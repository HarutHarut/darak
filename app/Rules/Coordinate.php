<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Coordinate implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $lat = '/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/';
        $long = '/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/';

        if ($attribute == 'lat') {
            return boolval(preg_match($lat, $value));
        }

        if ($attribute == 'long') {
            return boolval(preg_match($long, $value));
        }

        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'The given coordinate is invalid';
    }
}
