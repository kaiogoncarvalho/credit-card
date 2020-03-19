<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SearchCategoryRequest extends FormRequest
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
        $ruleIn = Rule::in(['name']);
        return [
            'name'     => 'string',
            'per_page' => 'integer',
            'page'     => 'integer',
            'order'    => [
                'string',
                $ruleIn
            ],
            'orders'   => 'array',
            'orders.*' => [
                'string',
                $ruleIn
            ],
        ];
    }
    
}
