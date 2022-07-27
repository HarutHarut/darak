<?php

namespace App\Http\Requests\Api\Social;

use Illuminate\Foundation\Http\FormRequest;

class Create extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize():bool
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
            'urls' => ['required','array'],
            'urls.*.type' => ['required', 'string'],
            'urls.*.url' => ['required', 'string'],
            'urls.*.business_id' => ['exists:businesses,id'],
            'urls.*.branch_id' => ['exists:branches,id']
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages():array
    {
        return [
            'urls.*.type.required' => 'The social network type field is required.',
            'urls.*.url.required' => 'The url field is required.',
            'urls.*.type.string' => 'The social network type must be string.',
            'urls.*.url.string' => 'The url must be string.',

            'urls.*.branch_id.exists' => 'The selected branch is not exist.',
            'urls.*.business_id.exists' => 'The selected business is not exist.',

        ];
    }
}
