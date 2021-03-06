<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\Brand;
use App\Rules\Double;

class UpdateCreditCardRequest extends FormRequest
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
        $id = $this->route('category_id');
        
        return [
            'name'         => "string|min:1|max:80|unique:credit_cards,name,{$id},id",
            'slug'         => "string|min:1|alpha_dash|unique:credit_cards,slug,{$id},id",
            'image'        => 'image',
            'brand'        => [
                Rule::in(Brand::getAll())
            ],
            'category_id'  => 'integer|exists:categories,id',
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
