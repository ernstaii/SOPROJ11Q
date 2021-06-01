<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppErrorRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'error_id' => ['required'],
            'message' => ['required', 'string'],
            'stacktrace' => ['nullable', 'string']
        ];
    }

    public function messages()
    {
        return [
            'error_id.required' => 'Vul a.u.b. een ID voor de foutmelding in.',
            'message.required' => 'Vul a.u.b. een foutmeldingsbericht in.',
            'message.string' => 'Zorg er a.u.b. voor dat het foutmeldingsbericht van het type "string" is.',
            'stacktrace.nullable' => 'Ongeldige waarde ingevuld voor stacktrace.',
            'stracktrace.string' => 'Zorg er a.u.b. voor dat de stacktrace van de foutmelding van het type "string" is.'
        ];
    }
}
