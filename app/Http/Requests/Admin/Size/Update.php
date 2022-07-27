<?php

namespace App\Http\Requests\Admin\Size;

use App\Rules\ExistsIfNotNull;
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
        $this->merge(['id' => $this->route('size')]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'id' => ['required', 'exists:sizes,id'],

            'name' => ['required', 'array'],
            'width' => ['required', 'numeric', 'min:1'],
            'verified' => ['required', 'numeric'],
            'height' => ['required', 'numeric', 'min:1'],
            'length' => ['required', 'numeric', 'min:1'],
            'desc' => ['required','array'],

        ];
    }
}
