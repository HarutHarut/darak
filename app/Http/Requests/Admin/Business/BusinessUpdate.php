<?php

namespace App\Http\Requests\Admin\Business;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BusinessUpdate extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => ['required'],
            'name' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', 'file', 'max:5000'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:businesses,email,' . $this->id],
            'currency' => ['string'],
            'address' => ['required', 'string'],
            'phone' => ['string'],
            'phone_country' => ['required', 'string'],
            'phone_code' => ['required', 'string'],
            'timezone' => ['required', 'string']
        ];
    }
}
