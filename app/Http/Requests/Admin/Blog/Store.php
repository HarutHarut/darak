<?php

namespace App\Http\Requests\Admin\Blog;

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
//            'user_id' => ['required', 'exists:users,id'],
//            'lang' => ['required', 'string', 'distinct'],
            'logo' => ['required', 'file', 'max:5000'],
//            'media.*.type' => [Rule::in(config('constants.media_types')), 'required_with:media'],
//            'media.*.file' => ['file', 'required_with:media'],

            'desc.en' => ['required', 'string'],
            'desc.ru' => ['nullable', 'string'],
            'desc.ch' => ['nullable', 'string'],
            'desc.am' => ['nullable', 'string'],
            'desc.fr' => ['nullable', 'string'],

            'title.en' => ['required', 'string'],
            'title.ru' => ['nullable', 'string'],
            'title.ch' => ['nullable', 'string'],
            'title.am' => ['nullable', 'string'],
            'title.fr' => ['nullable', 'string'],
        ];
    }
}
