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

    public function messages()
    {
        return [
            'location.required' => 'Vul a.u.b. een locatie in.',
            'location.string' => 'Vul a.u.b. een locatie in van het type "string".',
            'location.regex' => 'Vul a.u.b. een locatie in welke voldoet aan de voorwaarden. (Voorwaarden: 1 of 2 cijfers, gevolgd door een punt, gevolgd door x aantal cijfers. Tussen breedtegraad en lengtegraad een komma.)',
        ];
    }
}
