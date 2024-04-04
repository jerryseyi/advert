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
            ->with('device:id,name')
            ->select('device_id', View::raw('SUM(count) as count'))
            ->groupBy('device_id')
            ->get();

        return response()->json(['device' => $device, 'counts' => $counts]);
    }

    public function stats(User $user)
    {
        return $user->load(['device.views' => function ($query) use ($user) {
            $query
                ->where('customer_id', '=', $user->id)
                ->select('device_id', View::raw('SUM(count) as count'));
        }, 'uploads']);
    }
}
