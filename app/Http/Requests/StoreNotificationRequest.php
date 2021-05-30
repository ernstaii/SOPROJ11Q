<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNotificationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'message' => ['required', 'string'],
            'user_id' => ['nullable', 'integer']
        ];
    }

    public function messages()
    {
        return [
            'message.required' => 'Vul a.u.b. een bericht in voor de notificatie.',
            'message.string' => 'Vul a.u.b. een bericht in voor de notificatie van het type "string".',
            'user_id.nullable' => 'Ongeldige waarde opgegeven voor het gebruiker ID.',
            'user_id.integer' => 'Vul a.u.b. een getal in voor het gebruiker ID.'
        ];
    }
}
