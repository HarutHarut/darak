<?php

namespace App\Http\Requests\Api\Price;

use App\Rules\CheckPriceRange;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class Update extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request):array
    {
        $data = $request->all();

        return [
            'price.*.id' => ['required', 'exists:prices,id'],
            'price.*.range_start' => [ 'numeric'],
            'price.*.range_end' => [ 'numeric'],
            'price.*.price' => ['numeric'],

            'locker_id' => ['required','exists:lockers,id'],
            "price" => ['required','array', new CheckPriceRange($data['locker_id'])],

        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages():array
    {
        return [
            'price.*.id.required' => 'The id field is required.',
            'price.*.lat.range_start' => 'The range start must be numeric.',
            'price.*.lat.range_end' => 'The range end must be numeric.',
            'price.*.lat.price' => 'The price must be numeric.',
            'price.*.id.exists' => 'The selected price is not exist.',
        ];
    }
}
