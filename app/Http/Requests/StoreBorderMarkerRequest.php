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
            'lats' => ['array', 'min:3'],
            'lngs' => ['array', 'min:3']
        ];
    }

    public function messages()
    {
        return [
            'lats.array' => 'mapScript.js:114|type should be "array", different type provided.',
            'lngs.array' => 'mapScript.js:114|type should be "array", different type provided.'
        ];
    }
}
