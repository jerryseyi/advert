<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\User;
use App\Models\View;

class StatusController extends Controller
{
    public function index(Device $device)
    {
        $device = $device->load(['user', 'uploads']);

        $counts = View::query()
            ->where('customer_id', '=', $device->user_id)
            ->select('device_id', View::raw('SUM(count) as count'))
            ->groupBy('device_id')
            ->get();

        return response()->json(['device' => $device, 'counts' => $counts]);
    }

    public function stats(User $user)
    {
        $device = Device::where('user_id', auth()->id())->first();

        return $device->load(['user', 'uploads']);
    }
}
