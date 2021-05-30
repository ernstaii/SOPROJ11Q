<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLootRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'lats' => ['required', 'array', 'min:1'],
            'lngs' => ['required', 'array', 'min:1']
        ];
    }

    public function messages()
    {
        return [
            'lats.required' => 'Zorg er a.u.b. voor dat er een lijst aan breedtegraden wordt opgestuurd. (Buit)',
            'lats.array' => 'Zorg er a.u.b. voor dat er een lijst aan breedtegraden wordt opgestuurd. (Buit)',
            'lats.min' => 'Zorg er a.u.b. voor dat er minimaal één breedtegraad aanwezig is. (Buit)',
            'lngs.required' => 'Zorg er a.u.b. voor dat er een lijst aan lengtegraden wordt opgestuurd. (Buit)',
            'lngs.array' => 'Zorg er a.u.b. voor dat er een lijst aan lengtegraden wordt opgestuurd. (Buit)',
            'lngs.min' => 'Zorg er a.u.b. voor dat er minimaal één lengtegraad aanwezig is. (Buit)'
        ];
    }
}
