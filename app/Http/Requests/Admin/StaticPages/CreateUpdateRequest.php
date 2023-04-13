<?php

namespace App\Http\Requests\Admin\StaticPages;

use Illuminate\Foundation\Http\FormRequest;

class CreateUpdateRequest extends FormRequest
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
            'description_en' => ['required','string'],
            'description_ru' => ['string'],
            'description_ch' => ['string'],
            'description_am' => ['string'],
            'description_fr' => ['string'],

            'title.en' => ['required','string'],
            'title.ru' => ['string'],
            'title.ch' => ['string'],
            'title.am' => ['string'],
            'title.fr' => ['string'],
        ];
    }
}
