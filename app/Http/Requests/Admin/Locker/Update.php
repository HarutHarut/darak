<?php

namespace App\Http\Requests\Admin\Locker;

use Illuminate\Foundation\Http\FormRequest;

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
        $this->merge(['id' => $this->route('locker')]);
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'id' => ['required', 'exists:lockers,id'],
            'size_id' => [ 'exists:sizes,id'],
            'name' => ['string', 'max:255'],
            'count' => ['numeric'],
            'price_per_hour' => ['numeric'],
            'price_per_day' => ['numeric'],
        ];
    }

}
