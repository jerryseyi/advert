<?php

namespace App\Http\Controllers;

use App\Models\Upload;

class UploadStatusController extends Controller
{
    public function store(Upload $upload)
    {
        $upload->update([
            'disabled' => ! $upload->disabled
        ]);


        return response()->json(['message' => 'Device status updated']);
    }
}
