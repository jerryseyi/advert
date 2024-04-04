<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Upload;
use App\Models\User;
use App\Models\View;
use App\Policies\UploadPolicy;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UploadController extends Controller
{
    public function index(Request $request)
    {
        $device = Device::where('uid', $request->header('Device'))->firstOrFail();

        $deviceIdsToExclude = json_decode($device->upload_ids, true) ?? [];

        $uploads = Upload::query()
            ->where('disabled', false)
            ->WhereHas('device', function ($query) use ($deviceIdsToExclude) {
                if (! empty($deviceIdsToExclude)) {
                    foreach ($deviceIdsToExclude as $deviceId) {
                            $query->whereJsonDoesntContain('upload_ids', $deviceId);
                        }
                }
            })
            ->get();

        $uploads->each(function ($item) use ($device) {
            $view = View::where('upload_id', '=', $item->id)->first();

            if ($view) {
                $view->increment('count');
            } else {
                if (isset($device)) {
                    View::create([
                        'device_id' => $device->id,
                        'user_id' => $device->user_id ?? null,
                        'upload_id' => $item->id,
                        'customer_id' => $item->user_id,
                        'count' => 1,
                        'name' => $device->name
                    ]);
                }
            }
        });

        return $uploads;
    }

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
        if ($user->upload_count >= $maxUploads) {
            return response()->json(['error' => 'Maximum upload limit reached.'], 403);
        }

        // store the upload file.
        $image = $request->file('image');
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $image->storeAs('uploads', $imageName, 'public');

        // create a new upload record.
        $upload = new Upload();
        $upload->user_id = $user->id;
        $upload->device_id = $user->device()->first()->id;
        $upload->image = $imageName;
        $upload->size = $image->getSize();
        $upload->type = $image->getType();
        $upload->save();

        // increment the count
        $user->increment('upload_count');

        return response()->json(['message' => 'Upload Successful.']);
    }

    public function update(Request $request, User $user, Upload $upload)
    {
        if  ($user->max_tries <= $upload->max_tries) {
            abort(403, "You have exceeded the number of updates");
        }

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $upload = Upload::find($upload->id);

        $image = $request->file('image');
        $imageName = time() . '.' . $image->extension(); // Generate a unique name for the image
        $image->storeAs('uploads', $imageName, 'public'); // Store the image in the public storage directory

        // Update image path in the database
        $upload->image = $imageName;
        $upload->max_tries = $upload->max_tries + 1;
        $upload->save();

        return response()->json(['message' => 'Updated Successfully']);
    }
}
