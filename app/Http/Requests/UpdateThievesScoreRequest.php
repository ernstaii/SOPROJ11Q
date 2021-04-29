<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UpdateThievesScoreRequest extends FormRequest
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
            'thieves_score' => ['required', 'integer', 'min:0'],
            'police_score' => ['required', 'integer', 'min:0'],
        ];
    }
}
