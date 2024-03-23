<?php

namespace App\Policies;

use App\Models\Upload;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UploadPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {

    }

    public function view(User $user, Upload $upload): bool
    {
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->id === auth()->id();
    }

    public function update(User $user, Upload $upload): bool
    {
        return $user->isAdmin() || $user->id === $upload->user_id;
    }

    public function delete(User $user, Upload $upload): bool
    {
    }

    public function restore(User $user, Upload $upload): bool
    {
    }

    public function forceDelete(User $user, Upload $upload): bool
    {
    }
}
