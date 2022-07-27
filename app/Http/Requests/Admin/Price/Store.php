<?php

namespace App\Http\Requests\Admin\Price;

use App\Rules\CheckPriceRange;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class Store extends FormRequest
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
            'locker_id' => ['required','exists:lockers,id'],
            "price" => ['required','array', new CheckPriceRange($data['locker_id'])],
            'price.*.range_start' => ['required', 'numeric'],
            'price.*.range_end' => ['required', 'numeric', 'after:price.*.range_start'],
            'price.*.price' => ['required', 'numeric'],
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

            'price.*.range_start.required' => 'The range start field is required.',
            'price.*.range_end.required' => 'The range end field is required.',
            'price.*.range_type.required' => 'The range type field is required.',
            'price.*.price.required' => 'The price field is required.',


            'price.*.range_type.string' => 'The range type must be string.',
            'price.*.lat.range_start' => 'The range start must be numeric.',
            'price.*.lat.range_end' => 'The range end must be numeric.',
            'price.*.lat.price' => 'The price must be numeric.',

            'price.*.range_end.after' => 'The end range date must be a date after start range.'
        ];
    }
}
