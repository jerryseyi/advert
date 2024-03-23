<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index()
    {
        return Device::all();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'integer'],
            'name' => ['required'],
            'status' => ['required'],
            'expiration_date' => ['required', 'date'],
        ]);

        return Device::create($data);
    }

    public function show(Device $device)
    {
        return $device;
    }

    public function update(Request $request, Device $device)
    {
        $data = $request->validate([
            'user_id' => ['required', 'integer'],
            'name' => ['required'],
            'status' => ['required'],
            'expiration_date' => ['required', 'date'],
        ]);

        $device->update($data);

        return $device;
    }

    public function destroy(Device $device)
    {
        $device->delete();

        return response()->json();
    }
}
