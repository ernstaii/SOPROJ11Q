<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSpecialRoleRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'is_special_role' => ['required']
        ];
    }

    public function messages()
    {
        return [
            'is_special_role.required' => 'Geef a.u.b. aan of de gebruiker een speciale rol heeft.'
        ];
    }
}
