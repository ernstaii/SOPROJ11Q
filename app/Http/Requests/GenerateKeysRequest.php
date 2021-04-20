<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateKeysRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'input' => ['required', 'integer', 'between:1,50'],
            'ratio' => ['required', 'integer', 'between:0,100']
        ];
    }
}
