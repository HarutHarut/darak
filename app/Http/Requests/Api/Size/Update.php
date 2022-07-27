<?php

namespace App\Http\Requests\Api\Size;

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
    public function rules():array
    {
        return [
            'id' => ['exists:sizes,id'],
//            'branch_id' => ['exists:branches,id'],
//            'name' => ['string', 'max:255'],
            'width' => ['numeric'],
            'height' => ['numeric'],
            'length' => ['numeric'],
            'desc.en' => ['required','string'],
            'desc.ru' => ['string'],
            'desc.ch' => ['string'],
            'desc.am' => ['string'],
            'desc.fr' => ['string'],
            'name.en' => ['string'],
            'name.ru' => ['string'],
            'name.ch' => ['string'],
            'name.am' => ['string'],
            'name.fr' => ['string'],
        ];
    }


    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'size.*.name.string' => 'The name must be string.',
            'size.*.name.max' => 'The name must not be greater than 255 characters.',
            'size.*.branch_id.exists' => 'The selected branch is not exist.',
            'size.*.width.numeric' => 'The width must be numeric.',
            'size.*.height.numeric' => 'The height must be numeric.',
            'size.*.length.numeric' => 'The length must be numeric.',
            'size.*.desc.string' => 'The desc must be numeric.',
        ];
    }
}
