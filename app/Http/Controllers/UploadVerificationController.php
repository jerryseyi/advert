<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UploadVerificationController extends Controller
{
    public function store(Request $request, User $user)
    {
        $data = $request->validate([
            'max_upload' => 'required',
            'max_tries' => 'required',
            'expiration_date' => 'required'
        ]);

        $user->update([
            'max_upload' => $data['max_upload'],
            'max_tries' => $data['max_tries']
        ]);

        $user->device()->update([
           'expiration_date' => Carbon::parse($data['expiration_date'])
        ]);

        return response()->json(['message' => 'Requirements set successfully']);
    }
}
