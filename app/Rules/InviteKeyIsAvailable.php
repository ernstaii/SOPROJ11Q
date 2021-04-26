<?php

namespace App\Rules;

use App\Models\InviteKey;
use Illuminate\Contracts\Validation\Rule;

class InviteKeyIsAvailable implements Rule
{
    public function passes($attribute, $value)
    {
        if (InviteKey::whereValue($value)->first()->user == null)
            return true;
        return false;
    }

    public function message()
    {
        return 'De code is al in gebruik.';
    }
}
