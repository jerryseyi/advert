<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (! Auth::attempt($data)) {
            return response(['message' => 'Invalid Credentials'], 422);
        }

        $accessToken = Auth::user()->createToken('authToken')->accessToken;

        return response(['user' => Auth::user(), 'access_token' => $accessToken]);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6'
        ]);

        $device = $request->validate([
           'device_id' => 'required'
        ]);

        $device = Device::findOrFail($device['device_id']);

        $data['password'] = bcrypt($request->password);
        $data['role'] = 'customer';
        $user = User::create($data);

        $device->update([
           'user_id' =>  $user->id
        ]);

        $accessToken = $user->createToken('authToken')->accessToken;
        return response(['user' => $user, 'access_token' => $accessToken], 201);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response(['message' => 'Successfully logged out']);
    }
}
