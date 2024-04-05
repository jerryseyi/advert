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

        return response()->json(['device' => $device, 'counts' => $device->owners]);
    }

    public function stats(User $user)
    {
        return $user->load(['device.owners' => function ($query) use ($user) {
//            $query
//                ->where('customer_id', '=', $user->id);
//                ->select('device_id', View::raw('SUM(count) as count'), View::raw('COUNT(upload_id) as upload_count'));
        }, 'uploads']);
    }
}
