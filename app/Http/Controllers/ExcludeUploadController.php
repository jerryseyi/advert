<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Upload;
use Illuminate\Foundation\Events\DiscoverEvents;

class ExcludeUploadController extends Controller
{
    public function store(Device $device, Upload $upload)
    {
        if ( ! \Gate::allows('isAdmin')) {
            abort(301);
        }

        // Decode the existing upload_ids JSON into an array
        $uploadIds = json_decode($device->upload_ids, true) ?? [];

        //check if the value is in the array, remove it if found there and add it if not found.
        if (in_array($upload->id, $uploadIds)) {
            $key = array_search($upload->id, $uploadIds);
            if ($key !== false) {
                unset($uploadIds[$key]);
            }
        } else {
            $uploadIds[] = $upload->id;
        }


        // save the ids
        $device->upload_ids = $uploadIds;
        $device->save();

        return response()->json(['message' => 'image excluded successfully']);
    }
}
