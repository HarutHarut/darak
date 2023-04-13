<?php

namespace App\Http\Requests\Api\Branch;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class Create extends FormRequest
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
     * @param Request $request
     * @return array
     */
    public function rules(Request $request): array
    {
        $data = $request->all();
        $rules =  [
//            'business_id' => ["required", 'exists:businesses,id'],
            'city' => ['required', 'array'],
            'lat' => ['required', 'numeric'],
            'lng' => ['required', 'numeric'],
            'phone' => ['nullable', 'string'],

            'address' => ['required', 'string', 'max:255'],
            'logo' => ['required', 'file', 'max:5000'],
//            'logo' => ['required', 'file', 'max:5000', 'mimes:jpeg,png,jpg'],
            'media' => ['array'],
            'mediaKey' => ['array'],
            'status' => ['required', 'integer'],
            'email' => ['required'],
            'card_payment' => ['required', 'integer'],
            'is_bookable' => ['nullable'],

            'description.en' => ['required', 'string'],
            'description.ru' => ['nullable', 'string'],
            'description.ch' => ['nullable', 'string'],
            'description.am' => ['nullable', 'string'],
            'description.fr' => ['nullable', 'string'],

            'name.en' => ['required', 'string'],
            'name.ru' => ['nullable', 'string'],
            'name.ch' => ['nullable', 'string'],
            'name.am' => ['nullable', 'string'],
            'name.fr' => ['nullable', 'string'],

            'meta_title.en' => ['nullable', 'string'],
            'meta_title.ru' => ['nullable', 'string'],
            'meta_title.ch' => ['nullable', 'string'],
            'meta_title.am' => ['nullable', 'string'],
            'meta_title.fr' => ['nullable', 'string'],

            'meta_description.en' => ['nullable', 'string'],
            'meta_description.ru' => ['nullable', 'string'],
            'meta_description.ch' => ['nullable', 'string'],
            'meta_description.am' => ['nullable', 'string'],
            'meta_description.fr' => ['nullable', 'string'],

            'meta_keywords.en' => ['nullable', 'string'],
            'meta_keywords.ru' => ['nullable', 'string'],
            'meta_keywords.ch' => ['nullable', 'string'],
            'meta_keywords.am' => ['nullable', 'string'],
            'meta_keywords.fr' => ['nullable', 'string'],

            'socialMedia' => ['nullable'],
        ];
        if(isset($data['phone']) && $data['phone'] !== null){
            $rules['phone_country'] = ['required', 'string'];
            $rules['phone_code'] = ['required', 'string'];
        }

        return $rules;
    }
}
