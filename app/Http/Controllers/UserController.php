<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function store(Request $request)
    {
        $this->validateRequest($request);
        $user = new User();
        $user->username = $request->get('username');
        $user->location = $request->get('location');
        $user->save();
    }

    public function update(Request $request, $id)
    {
        $this->validateRequest($request);
        $user = User::find($id);
        $user->location = $request->get('location');
        $user->save();
    }

    private function validateRequest(Request $request) {
        $request->validate([
            'username' => 'required|min:3|max:255',
            'location' => 'nullable|regex:/^([-+]?)([\d]{1,2})(((\.)(\d+)(,)))(\s*)(([-+]?)([\d]{1,3})((\.)(\d+))?)$/i'
        ]);
    }
}
