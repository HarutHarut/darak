<?php

namespace App\Http\Requests\Admin\City;

use App\Rules\Coordinate;
use Illuminate\Foundation\Http\FormRequest;


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
    public function rules(): array
    {
        return [
            'image' => ['nullable'],
            'preview' => ['nullable'],
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
        ];
    }
}
