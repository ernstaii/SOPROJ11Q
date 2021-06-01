<?php

namespace App\Http\Requests;

use App\Rules\InviteKeyIsAvailable;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
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
            'username' => ['required', 'string', 'between:3,255'],
            'location' => ['nullable', 'string', 'regex:/^([-+]?)([\d]{1,2})(((\.)(\d+)(,)))(\s*)(([-+]?)([\d]{1,3})((\.)(\d+))?)$/i'],
            'invite_key' => ['required', 'string', 'size:4', 'exists:invite_keys,value', new InviteKeyIsAvailable()],
        ];
    }

    public function messages()
    {
        return [
            'username.required' => 'Vul a.u.b. een gebruikersnaam in.',
            'username.string' => 'Vul a.u.b. een gebruikersnaam in van het type "string".',
            'username.between' => 'Vul a.u.b. een gebruikersnaam in met minimaal 3 tekens en maximaal 255 tekens.',
            'location.nullable' => 'Ongeldige waarde opgegeven voor de locatie.',
            'location.string' => 'Vul a.u.b. een locatie in van het type "string".',
            'location.regex' => 'Vul a.u.b. een locatie in welke voldoet aan de voorwaarden. (Voorwaarden: 1 of 2 cijfers, gevolgd door een punt, gevolgd door x aantal cijfers. Tussen breedtegraad en lengtegraad een komma.)',
            'invite_key.required' => 'Vul a.u.b. een toegangscode in.',
            'invite_key.string' => 'Vul a.u.b. een toegangscode in van het type "string".',
            'invite_key.size' => 'Vul a.u.b. een toegangscode in welke uit 4 tekens bestaat.',
            'invite_key.exists' => 'Vul a.u.b. een geldige toegangscode in.'
        ];
    }
}
