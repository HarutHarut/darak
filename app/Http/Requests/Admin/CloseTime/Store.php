<?php

namespace App\Http\Requests\Admin\CloseTime;

use Illuminate\Foundation\Http\FormRequest;

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
    public function rules():array
    {
        return [
            'time.*.branch_id' => ['required', 'exists:branches,id'],
            'time.*.start' => ['required', 'date'],
            'time.*.end' => ['required', 'date', 'after:time.*.start'],
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
            'time.*.branch_id.required' => 'The branch field is required.',
            'time.*.end.required' => 'The end field is required.',
            'time.*.start.required' => 'The start field is required.',
            'time.*.end.date' => 'The end date must be date.',
            'time.*.start.date' => 'The start date must be date.',
            'time.*.branch_id.exists' => 'The selected branch is not exist.',
            'time.*.end.after' => 'The end date must be a date after start date.'
        ];
    }
}
