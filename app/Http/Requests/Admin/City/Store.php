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
            'preview' => ['image'],
            'top'   => ['numeric'],

            'description.en' => ['required', 'string'],
            'description.ru' => ['nullable', 'string'],
            'description.ch' => ['nullable', 'string'],
            'description.am' => ['nullable', 'string'],
            'description.fr' => ['nullable', 'string'],

            'about_city.en' => ['required', 'string'],
            'about_city.ru' => ['nullable', 'string'],
            'about_city.ch' => ['nullable', 'string'],
            'about_city.am' => ['nullable', 'string'],
            'about_city.fr' => ['nullable', 'string'],

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
        ];
    }
}
