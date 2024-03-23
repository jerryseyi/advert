<?php

namespace App\Http\Controllers;

use App\Models\Upload;
use App\Models\User;
use App\Policies\UploadPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UploadController extends Controller
{
    public function store(User $user, Request $request)
    {
        $this->authorize('create', Upload::class);

        $rules = [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
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

        $maxUploads = $user->max_upload;

        // abort if maximum upload limit reached.
        if ($user->uploads()->count() >= $maxUploads) {
            return response()->json(['error' => 'Maximum upload limit reached.'], 403);
        }

        // store the upload file.
        $image = $request->file('image');
        $imageName = time().'.'.$image->getClientOriginalExtension();
        $image->storeAs('public/uploads', $imageName);

        // create a new upload record.
        $upload = new Upload();
        $upload->user_id = $user->id;
        $upload->device_id = $user->device()->first()->id;
        $upload->image = $imageName;
        $upload->size = $image->getSize();
        $upload->type = $image->getType();
        $upload->save();

        return response()->json(['message' => 'Upload Successful.']);
    }

    public function update(Request $request, User $user, Upload $upload)
    {
        $this->authorize('update', $upload);

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $upload = Upload::find($upload->id);

        $image = $request->file('image');
        $imageName = time().'.'.$image->extension(); // Generate a unique name for the image
        $image->storeAs('public/uploads', $imageName); // Store the image in the public storage directory

        // Update image path in the database
        $upload->image = 'storage/uploads/'.$imageName;
        $upload->save();

        return response()->json(['message' => 'Updated Successfully']);
    }
}
