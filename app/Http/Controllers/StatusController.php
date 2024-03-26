<?php

namespace App\Http\Controllers;

use App\Models\Device;

class StatusController extends Controller
{
    public function index(Device $device)
    {
        return $device->with(['user', 'uploads'])->first();
    }
}
