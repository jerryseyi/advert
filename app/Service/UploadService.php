<?php

namespace App\Service;

use App\Models\Upload;

class UploadService
{
    public function getUploads($deviceIdsToExclude)
    {
        return Upload::query()
            ->where('disabled', false)
            ->WhereHas('device', function ($query) use ($deviceIdsToExclude) {
                if (! empty($deviceIdsToExclude)) {
                    foreach ($deviceIdsToExclude as $deviceId) {
                        $query->whereJsonDoesntContain('upload_ids', $deviceId);
                    }
                }
            })
            ->get();
    }
}
