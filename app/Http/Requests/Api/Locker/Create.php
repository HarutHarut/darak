<?php

namespace App\Http\Requests\Api\Locker;

use App\Rules\CheckPriceRange;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

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
     * @param Request $request
     * @return array
     */
    public function rules(Request $request): array
    {
        $data = $request->all();
//dd($data);
        $rules = [
            'branch_id' => ['required', 'exists:branches,id'],
            'size_id' => ['required', 'exists:sizes,id'],
            'count' => ['required','numeric'],
//            "prices" => ['required','array', new CheckPriceRange()],
        ];
        if(isset($data['price_per_hour']) || isset($data['price_per_day'])){
            if (count($data['prices'])) {
                $rules['prices'] = ['array', new CheckPriceRange()];
            }
            else{
                $rules['prices'] = ['array'];
            }
        }else{
            $rules['prices'] = ['required', 'array', new CheckPriceRange()];
        }

        if (count($data['prices'])) {
            if ((isset($data['prices'][0]['range_start']) || $data['prices'][0]['range_start'] !== null) &&
                (isset($data['prices'][0]['range_end']) || $data['prices'][0]['range_end'] !== null) &&
                (isset($data['prices'][0]['price']) || $data['prices'][0]['price'] !== null)
            ) {
                $rules['price_per_day'] = ['numeric', 'nullable'];
                $rules['price_per_hour'] = ['numeric', 'nullable'];
            } else {
                if (isset($data['price_per_hour'])) {
                    $rules['price_per_day'] = ['numeric', 'nullable'];
                } else {
                    $rules['price_per_day'] = ['required', 'numeric', 'nullable'];

                }
                if (isset($data['price_per_day'])) {
                    $rules['price_per_hour'] = ['numeric', 'nullable'];
                } else {
                    $rules['price_per_hour'] = ['required', 'numeric', 'nullable'];

                }
            }
        }

        return $rules;

//        return [
//            'branch_id' => ['required', 'exists:branches,id'],
//            'size_id' => ['required', 'exists:sizes,id'],
//            'count' => ['required','numeric'],
//            'price_per_hour' => ['numeric', 'nullable'],
//            'price_per_day' => ['numeric', 'nullable'],
//            "prices" => ['required','array', new CheckPriceRange()],
//        ];
    }
}
