<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Upload;
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

        if  ($data['expiration_date'] < now()) {
            Upload::where('device_id', $device->id)->get()
                ->each(function ($upload) {
                    $upload->disabled = true;
                    $upload->save();
                });
        }

        return $device;
    }

    public function destroy(Device $device)
    {
        // delete uploads associated with the device
        Upload::where('device_id', $device->id)->get()->each(function ($upload) { $upload->delete(); });

        // delete device
        $device->delete();

        return response()->json();
    }
}
