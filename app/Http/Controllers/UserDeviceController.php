<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;

class UserDeviceController extends Controller
{
    public function update(Request $request, Device $device)
    {
        $data = $request->validate([
           'user_id' => 'required',
        ]);

        $device->update([
           'user_id' => $data['user_id']
        ]);

        return response()->json(['message' => 'User added']);
    }
}
