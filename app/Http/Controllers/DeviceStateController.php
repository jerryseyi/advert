<?php

namespace App\Http\Controllers;

use App\Models\Device;

class DeviceStateController extends Controller
{
    public function disconnect(Device $device)
    {
        $device->update([
           'disabled' => true
        ]);

        return response()->json(['message' => 'device disconnected successfully']);
    }
}
