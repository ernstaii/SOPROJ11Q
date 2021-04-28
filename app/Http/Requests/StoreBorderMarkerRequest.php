<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBorderMarkerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'lats' => 'array',
            'lngs' => 'array'
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
