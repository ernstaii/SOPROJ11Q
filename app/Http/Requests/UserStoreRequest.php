<?php

namespace App\Http\Requests;

use App\Models\InviteKey;
use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'username' => ['required', 'between:3,255'],
            'location' => ['nullable', 'regex:/^([-+]?)([\d]{1,2})(((\.)(\d+)(,)))(\s*)(([-+]?)([\d]{1,3})((\.)(\d+))?)$/i'],
            'invite_key' => ['required', 'exists:invite_keys,value', 'unique:users,invite_key']
        ];
    }
}
