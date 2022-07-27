<?php

namespace App\Http\Requests\Api\Business;

use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize():bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules():array
    {
        return [
            'lat' => ['numeric'],
            'lng' => ['numeric'],
            'phone' => ['nullable','string', 'max:255'],
            'address' => ['nullable','string', 'max:255'],
            'currency' => ['nullable','string', 'max:255'],
            'logo' => ['image','max:5000'],

            'name' => ['string'],
        ];
    }
}
