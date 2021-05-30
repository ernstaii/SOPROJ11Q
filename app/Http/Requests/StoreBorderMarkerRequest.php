<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBorderMarkerRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'lats' => ['required', 'array', 'min:3'],
            'lngs' => ['required', 'array', 'min:3']
        ];
    }

    public function messages()
    {
        return [
            'lats.required' => 'Zorg er a.u.b. voor dat er een lijst aan breedtegraden wordt opgestuurd. (Grens-pin)',
            'lats.array' => 'Zorg er a.u.b. voor dat er een lijst aan breedtegraden wordt opgestuurd. (Grens-pin)',
            'lats.min' => 'Zorg er a.u.b. voor dat er minimaal drie breedtegraden aanwezig zijn. (Grens-pin)',
            'lngs.required' => 'Zorg er a.u.b. voor dat er een lijst aan lengtegraden wordt opgestuurd. (Grens-pin)',
            'lngs.array' => 'Zorg er a.u.b. voor dat er een lijst aan lengtegraden wordt opgestuurd. (Grens-pin)',
            'lngs.min' => 'Zorg er a.u.b. voor dat er minimaal drie lengtegraden aanwezig zijn. (Grens-pin)'
        ];
    }
}
