<?php

namespace App\Http\Requests\Api\CloseTime;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTime extends FormRequest
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
            'time.*.branch_id' => ['exists:branches,id'],
            'time.*.end' => ['date'],
            'time.*.start' => ['date'],
            'time.*.id' => ['exists:closing_times,id']


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
            'time.*.end.date' => 'The end date  must be date.',
            'time.*.start.date' => 'The start date must be date.',
            'time.*.branch_id.exists' => 'The selected branch is not exist.'
        ];
    }
}
