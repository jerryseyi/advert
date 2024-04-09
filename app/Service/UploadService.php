<?php

namespace App\Service;

use App\Models\Upload;
use Illuminate\Support\Collection;

class UploadService
{
    public function getUploads($deviceIdsToExclude): Collection
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

    public function getExcludedImages($ids): Collection
    {
        return Upload::query()
                    ->whereIn('id', $ids)
                    ->get();
    }
}
