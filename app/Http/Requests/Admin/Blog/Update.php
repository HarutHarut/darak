<?php

namespace App\Http\Requests\Admin\Blog;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
//        $this->merge(['blog_id' => $this->route('blog')]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
//            'blog_id' => ['required', 'exists:blogs,id'],
//            'translation_id' => ['required'],
//            'title' => ['required', 'string', 'max:255'],
//            'desc' => ['required', 'string'],
//            'lang' => ['required', 'string', 'distinct'],
//            'logo' => ['required', 'max:5000'],
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
