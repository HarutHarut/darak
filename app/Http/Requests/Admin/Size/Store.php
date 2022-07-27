<?php

namespace App\Http\Requests\Admin\Size;

use App\Rules\ExistsIfNotNull;
use Illuminate\Foundation\Http\FormRequest;

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
            'name' => ['required', 'array'],
            'width' => ['required', 'numeric', 'min:1'],
            'verified' => ['required', 'numeric'],
            'height' => ['required', 'numeric', 'min:1'],
            'length' => ['required', 'numeric', 'min:1'],
            'desc' => ['required','array'],
//            'desc.en' => ['required','string'],
//            'desc.ru' => ['string'],
//            'desc.ch' => ['string'],
//            'desc.am' => ['string'],
//            'desc.fr' => ['string'],
//            'name.en' => ['required','string'],
//            'name.ru' => ['string'],
//            'name.ch' => ['string'],
//            'name.am' => ['string'],
//            'name.fr' => ['string'],
        ];
    }
}
