<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLocationRequest extends FormRequest
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
            'location' => ['required', 'string', 'regex:/^([-+]?)([\d]{1,2})(((\.)(\d+)(,)))(\s*)(([-+]?)([\d]{1,3})((\.)(\d+))?)$/i']
        ];
    }
}
