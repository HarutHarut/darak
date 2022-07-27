<?php

namespace App\Http\Requests\Api\Blog;

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
            'blog_id' => ['required', 'exists:blogs,id'],
            'id' => ['exists:blog_translations,id'],
            'title' => ['string', 'max:255'],
            'desc' => ['string'],
            'lang' => ['required', 'string', 'distinct'],
        ];
    }
}
