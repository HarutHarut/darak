<?php

namespace App\Http\Requests\Admin\Branch;

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
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        $this->merge(['id' => $this->route('branch')]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'id' => ['required', 'exists:branches,id'],
            'lat' => [ 'numeric'],
            'lng' => [ 'numeric'],
            'phone' => [ 'string', 'max:255'],
            'name' => [ 'string', 'max:255'],
            'city' => [ 'string', 'max:255'],
            'country' => [ 'string', 'max:255'],
            'address' => [ 'string', 'max:255'],
            'currency' => [ 'string', 'max:255'],
            'status' => Rule::in(config('constants.branch_status')),
            'working_status' =>  Rule::in(config('constants.branch_working_status')),
        ];
    }
}
