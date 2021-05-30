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

    public function messages()
    {
        return [
            'input.required' => 'Vul a.u.b. een getal voor het aantal deelnemers in.',
            'input.integer' => 'Vul a.u.b. een getal voor het aantal deelnemers in.',
            'input.between' => 'Er moet minstens één deelnemer zijn. Daarnaast mogen er maximaal 50 deelnemers meedoen.',
            'ratio.required' => 'Selecteer a.u.b. een waarde voor het ratio agenten:boeven.',
            'ratio.integer' => 'Selecteer a.u.b. een numerieke waarde voor het ratio agenten:boeven.',
            'ratio.between' => 'Het ratio agenten:boeven mag minimaal 0% en maximaal 100% zijn.'
        ];
    }
}
