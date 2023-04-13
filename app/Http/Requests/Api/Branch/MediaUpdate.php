<?php

namespace App\Http\Requests\Api\Branch;

use Illuminate\Foundation\Http\FormRequest;

class MediaUpdate extends FormRequest
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
            'id' => ['required', 'exists:branches,id'],
            'media' => ['nullable'],
            'mediaKey' => ['array'],

            'logo' => ['nullable', 'file', 'max:5000'],
        ];
    }
}
