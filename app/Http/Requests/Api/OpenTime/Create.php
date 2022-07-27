<?php

namespace App\Http\Requests\Api\OpenTime;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class Create extends FormRequest
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
            'branch_id' => ['required', 'exists:branches,id'],
            'time.*.weekday' => ['required', 'numeric', Rule::in(config('constants.days_of_week')),'distinct'],
            'time.*.start' => ['required'],
            'time.*.end' => ['required', 'after:time.*.start'],
            'time.*.status' => ['required'],
            'time' => ['size:7']
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
            'time.*.weekday.required' => 'The weekday field is required.',
            'time.*.end.required' => 'The end field is required.',
            'time.*.start.required' => 'The start field is required.',
            'time.*.status.required' => 'The status field is required.',
            'time.*.end.date' => 'The end date must be date.',
            'time.*.start.date' => 'The start date must be date.',
            'time.*.end.after' => 'The end date must be a date after start date.',
            'time.*.status.numeric' => 'The status must be numeric.',
        ];
    }
}
