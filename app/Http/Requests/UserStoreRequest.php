<?php

namespace App\Http\Requests;

use App\Enums\Roles;
use App\Enums\Statuses;
use App\Rules\InviteKeyIsAvailable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'username' => ['required', 'string', 'between:3,255'],
            'location' => ['nullable', 'string', 'regex:/^([-+]?)([\d]{1,2})(((\.)(\d+)(,)))(\s*)(([-+]?)([\d]{1,3})((\.)(\d+))?)$/i'],
            'invite_key' => ['required', 'string', 'size:4', 'exists:invite_keys,value', new InviteKeyIsAvailable()],
        ];
    }
}
