<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use \Illuminate\Contracts\Validation\Validator;

class GenerateKeysRequest extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        response()->json($validator->errors(), 422)->throwResponse();
    }

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'input' => ['required', 'integer', 'between:1,50'],
            'ratio' => ['required', 'integer', 'between:0,100'],
        ];
    }
}
