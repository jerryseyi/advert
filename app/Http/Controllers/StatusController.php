<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\User;

class StatusController extends Controller
{
    public function index(Device $device)
    {
        return $device->load(['user', 'uploads']);
    }

    public function stats(User $user)
    {
        $device = Device::where('user_id', auth()->id())->first();

        return $device->load(['user', 'uploads']);
    }
}
