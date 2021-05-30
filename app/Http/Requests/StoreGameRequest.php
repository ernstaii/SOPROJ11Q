<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGameRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'min:3', 'unique:games,name'],
            'password' => ['required', 'string', 'min:5']
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Vul a.u.b. een naam voor het spel in.',
            'name.string' => 'Vul a.u.b. een naam voor het spel in van het type "string".',
            'name.min' => 'Vul a.u.b een naam voor het spel in welke uit tenminste drie tekens bestaat.',
            'name.unique' => 'Deze spelnaam bestaat al! Kies a.u.b. een andere.',
            'password.required' => 'Vul a.u.b. een wachtwoord voor het spel in.',
            'password.string' => 'Vul a.u.b. een wachtwoord voor het spel in van het type "string".',
            'password.min' => 'Vul a.u.b. een wachtwoord voor het spel in welke uit tenminste vijf tekens bestaat.'
        ];
    }
}
