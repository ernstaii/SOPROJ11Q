<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePoliceStationLocationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'lat' => ['required'],
            'lng' => ['required']
        ];
    }

    public function messages()
    {
        return [
            'lat.required' => 'Vul a.u.b. een breedtegraad in voor het politiestation.',
            'lng.required' => 'Vul a.u.b. een lengtegraad in voor het politiestation.',
        ];
    }
}
