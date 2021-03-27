<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'id' => 'nullable',
            'username' => 'required|min:3|max:255',
            'location' => 'nullable|regex:/^([-+]?)([\d]{1,2})(((\.)(\d+)(,)))(\s*)(([-+]?)([\d]{1,3})((\.)(\d+))?)$/i'
        ]);
        $user = new User();
        $user->id = $request->get('id');
        $user->username = $request->get('username');
        $user->location = $request->get('location');
        $user->save();
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'location' => 'nullable|regex:/^([-+]?)([\d]{1,2})(((\.)(\d+)(,)))(\s*)(([-+]?)([\d]{1,3})((\.)(\d+))?)$/i'
        ]);
        $user->location = $request->get('location');
        $user->save();
    }
}
