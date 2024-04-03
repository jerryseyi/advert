<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index()
    {
        return Device::with('user')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'uid' => 'required',
            'name' => ['required'],
            'location' => ['required'],
            'expiration_date' => ['required']
        ]);

        return Device::create($data);
    }

    public function show(Device $device)
    {
        return $device->load('user');
    }

    public function update(Request $request, Device $device)
    {
        $data = $request->validate([
            'name' => ['required'],
            'location' => ['required'],
            'uid' => ['required'],
            'expiration_date' => ['required']
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
