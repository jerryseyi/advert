<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeviceUploadsController extends Controller
{
    public function index(Device $device, Request $request)
    {
        return $device->uploads;
    }
    public function store(Device $device, Request $request)
    {
//        $this->authorize('create', Upload::class);

        $rules = [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif'
        ];

        $messages = [
            'file.required' => 'Please upload a file.',
            'file.image' => 'The uploaded file must be an image.',
            'file.mimes' => 'The uploaded file must be of type: jpeg, png, jpg, gif.',
            'file.max' => 'The uploaded file may not be greater than 2MB.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $maxUploads = $device->user->max_upload;

        // abort if maximum upload limit reached.
        if ($device->user->upload_count >= $maxUploads && auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'Maximum upload limit reached.'], 403);
        }

        // store the upload file.
        $image = $request->file('image');
        $imageName = time().'.'.$image->getClientOriginalExtension();
        $image->storeAs('uploads', $imageName, 'public');

        // create a new upload record.
        $upload = new Upload();
        $upload->user_id = $device->user->id;
        $upload->device_id = $device->id;
        $upload->image = $imageName;
        $upload->size = $image->getSize();
        $upload->type = $image->getType();
        $upload->save();

        // increment the count
        $device->user->increment('upload_count');

        return response()->json(['message' => 'Upload Successful.']);
    }

    public function update(Request $request, Device $device, Upload $upload)
    {
        $this->authorize('update', $upload);

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $upload = Upload::find($upload->id);

        $image = $request->file('image');
        $imageName = time() . '.' . $image->extension(); // Generate a unique name for the image
        $image->storeAs('public/uploads', $imageName); // Store the image in the public storage directory

        // Update image path in the database
        $upload->image = '/uploads/' . $imageName;
        $upload->disabled = true;
        $upload->save();

        return response()->json(['message' => 'Updated Successfully']);
    }
}
