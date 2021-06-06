<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGadgetRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'gadget_name' => ['required', 'string', 'exists:gadgets,name'],
            'operator' => ['required', 'string']
        ];
    }
}
