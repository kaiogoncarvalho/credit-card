<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\Brand;
use App\Rules\Double;

class CreateCreditCardRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'         => 'required|string|min:1|max:80|unique:credit_cards,name',
            'slug'         => 'required|string|min:1|max:255|unique:credit_cards,slug',
            'image'        => 'required|image',
            'brand'        => [
                'required',
                Rule::in(Brand::getAll())
            ],
            'category_id'  => 'required|integer|exists:categories,id',
            'credit_limit' => [
                'nullable',
                new Double(15, 2)
            ],
            'annual_fee'   => [
                'nullable',
                new Double(15, 2)
            ]
        ];
    }
}
