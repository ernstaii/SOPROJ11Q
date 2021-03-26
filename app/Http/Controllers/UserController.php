<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validateWithBag('userPost', [
            'username' => 'required|min:3|max:255',
            'location' => 'nullable|regex:/^([-+]?)([\d]{1,2})(((\.)(\d+)(,)))(\s*)(([-+]?)([\d]{1,3})((\.)(\d+))?)$/i'
        ]);

        $user = new User();
        $user->username = $request->get('username');
        $user->location = $request->get('location');
        $user->save();
    }
}
