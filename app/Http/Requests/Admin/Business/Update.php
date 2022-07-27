<?php

namespace App\Http\Requests\Admin\Business;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
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

        $this->merge(['id' => $this->route('business')]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'id' => ['required', 'exists:businesses,id'],
            'name' => ['string', 'max:255'],
            'lat' => ['numeric'],
            'lng' => ['numeric'],
            'phone' => ['string', 'max:255'],
            'city' => ['string', 'max:255'],
            'address' => ['string', 'max:255'],
            'currency' => ['string', 'max:255'],
            'status' => ['numeric', Rule::in(config('constants.business_status'))],
            'publish' => ['numeric', Rule::in(config('constants.business_publish'))],
        ];
    }
}
