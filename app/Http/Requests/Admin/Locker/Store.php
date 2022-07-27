<?php

namespace App\Http\Requests\Admin\Locker;

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
    public function rules(): array
    {
        return [
            'branch_id' => ['required', 'exists:branches,id'],
            'size_id' => ['required', 'exists:sizes,id'],
            'name' => ['required', 'string', 'max:255'],
            'count' => ['required','numeric'],
            'price_per_hour' => ['numeric'],
            'price_per_day' => ['numeric'],
        ];
    }
}
