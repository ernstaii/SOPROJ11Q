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
}
