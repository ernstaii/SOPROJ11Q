<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\Console\Input\Input;

class UserController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validateWithBag('userPost', [
            'username' => 'required|min:3|max:255',
            'location' => 'nullable|regex:/^([-+]?)([\d]{1,2})(((\.)(\d+)(,)))(\s*)(([-+]?)([\d]{1,3})((\.)(\d+))?)$/i'
        ]);

        $user = new User();
        $user->username = Input::get('username');
        $user->location = Input::get('location');
        $user->save();
    }
}
