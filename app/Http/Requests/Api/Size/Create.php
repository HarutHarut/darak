<?php

namespace App\Http\Requests\Api\Size;

use Illuminate\Foundation\Http\FormRequest;

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
     * @return array
     */
    public function rules(): array
    {
        return [
            //'name' => ['required', 'array'],
            //'desc' => ['required', 'array'],
            'width' => ['required', 'numeric', 'min:1'],
            'height' => ['required', 'numeric', 'min:1'],
            'length' => ['required', 'numeric', 'min:1'],
            'desc.en' => ['required','string'],
            'desc.ru' => ['string'],
            'desc.ch' => ['string'],
            'desc.am' => ['string'],
            'desc.fr' => ['string'],
            'name.en' => ['required','string'],
            'name.ru' => ['string'],
            'name.ch' => ['string'],
            'name.am' => ['string'],
            'name.fr' => ['string'],
        ];
    }

}
