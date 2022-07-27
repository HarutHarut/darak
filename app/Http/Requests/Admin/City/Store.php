<?php

namespace App\Http\Requests\Admin\City;

use App\Rules\Coordinate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
    public function rules(): array
    {
        return [
            'name' => ['string', 'max:255', 'required'],
            'lat' => ['numeric'],
            'lng' => ['numeric'],
            'image' => ['image'],
            'top'   => ['numeric'],
        ];
    }
}
