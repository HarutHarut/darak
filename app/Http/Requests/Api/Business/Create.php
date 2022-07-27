<?php

namespace App\Http\Requests\Api\Business;

use Illuminate\Foundation\Http\FormRequest;

class Create extends FormRequest
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
            'city_id' => ['required', 'exists:cities,id'],
            'currency_id' => ['required', 'exists:currencies,id'],
            'lat' => ['required', 'numeric'],
            'lng' => ['required', 'numeric'],
            'phone' => ['required', 'string', 'max:255'],
            'city' => ['string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'logo' => ['image', 'file', 'max:5000'],


            'name.en' => ['required','string'],
            'name.ru' => ['string'],
            'name.ch' => ['string'],
            'name.am' => ['string'],
            'name.fr' => ['string'],
        ];
    }
}
