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
            'lats.array' => 'mapScript.js:255|type should be "array", different type provided.',
            'lngs.array' => 'mapScript.js:255|type should be "array", different type provided.'
        ];
    }
}
