<?php

namespace App\Http\Requests\Api\OpenTime;

use Illuminate\Foundation\Http\FormRequest;
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
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules():array
    {
        return [
            'branch_id' => ['required'],
            'time.*.id' => ['exists:opening_times,id'],
            'time.*.start' => ['required'],
//            'time.*.end' => ['required', 'after:time.*.start'],
            'time.*.end' => ['required'],
            'time.*.status' => ['required'],
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
            'time.*.end.string' => 'The name must be string.',
            'time.*.start.string' => 'The start must be string.',
            'time.*.status.numeric' => 'The status must be numeric.',
            'time.*.branch_id.exists' => 'The selected branch is not exist.',
        ];
    }
}
