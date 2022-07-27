<?php

namespace App\Http\Requests\Admin\User;

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
//        $this->merge(['id' => $this->route('user')]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'min:2'],
            'last_name' => ['required', 'string', 'max:255', 'min:2'],
            'email' => 'required|email|unique:users,id,' . $this->route('user'),
            'business_name' => ['string', 'nullable'],
            'currency' => ['string', 'nullable'],
            'id' => ['required'],
            'role_id' => ['required', 'not_in:1','exists:roles,id'],
            'status' => ['required', Rule::in(config('constants.user_status'))],
        ];
    }
}
