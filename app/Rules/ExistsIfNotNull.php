<?php

namespace App\Rules;

use App\Models\Branch;
use Illuminate\Contracts\Validation\Rule;

class ExistsIfNotNull implements Rule
{

    private $attribute;

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $this->attribute = $attribute;

        if ($attribute == 'branch_id') {
            return Branch::query()->where('id', '=', $value)->exists();
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
        return 'The selected ' . str_replace('_', ' ', $this->attribute) . ' is invalid';
    }
}
