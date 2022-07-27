<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class Register extends FormRequest
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
     * @param Request $request
     * @return array
     */
    public function rules(Request $request):array
    {
        $data = $request->all();

        $rules = [
            'name' => ['required', 'string', 'max:255', 'min:2'],
            'last_name' => ['required', 'string', 'max:255', 'min:2'],
//            'email' => 'required|email|unique:businesses,email,' . $this->email,
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required_with:password_confirmation ', 'same:password_confirmation', 'string', 'min:6', 'confirmed'],
            'business_name' => ['string'],
            'currency' => ['string'],
            'phone' => ['string'],
            'privacy_policy' => ['required'],
            'timezone' => ['timezone']
        ];
        if (isset($data['phone_country']) && isset($data['phone_code'])){
            $rules['phone_country'] = ['required', 'string'];
            $rules['phone_code'] = ['required', 'string'];
        }

        return $rules;
    }
}
