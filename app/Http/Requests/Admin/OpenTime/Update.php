<?php

namespace App\Http\Requests\Admin\OpenTime;

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
    public function rules(): array
    {
        return [
            'time.*.id' => ['exists:opening_times,id'],
            'time.*.branch_id' => ['exists:branches,id'],
            'time.*.weekday' => ['numeric', Rule::in(config('constants.days_of_week'))],
            'time.*.end' => ['string', 'max:255'],
            'time.*.start' => ['string', 'max:255'],
            'time.*.status' => ['numeric', Rule::in(config('constants.branch_open_status'))]
        ];
    }


    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'time.*.end.string' => 'The name must be string.',
            'time.*.start.string' => 'The start must be string.',
            'time.*.status.numeric' => 'The status must be numeric.',
            'time.*.branch_id.exists' => 'The selected branch is not exist.',
        ];
    }
}
