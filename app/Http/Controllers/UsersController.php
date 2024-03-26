<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index()
    {
        return User::all();
    }

    public function update(User $user, Request $request)
    {
        $data = $request->validate([
           'name' => 'required',
            'email' => 'required',
            'max_upload' => 'required',
            'max_tries' => 'required'
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->max_upload = $data['max_upload'];
        $user->max_tries = $data['max_tries'];

        $user->save();

        return response()->json(['message' => 'customers edited successfully']);
    }
}
