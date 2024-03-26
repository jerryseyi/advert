<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\User;

class StatusController extends Controller
{
    public function index(Device $device)
    {
        return $device->with(['user', 'uploads'])->first();
    }

    public function stats(User $user)
    {
        $device = Device::where('user_id', $user->id)->first();

        return $device->with(['user', 'uploads'])->first();
    }
}
