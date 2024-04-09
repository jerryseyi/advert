<?php

namespace App\Service;

use App\Models\Upload;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class UploadService
{
    public function getUploads($deviceIdsToExclude): Collection
    {
        return Upload::query()
            ->where('disabled', false)
            ->whereNotIn('id', $deviceIdsToExclude)
            ->get();
    }

    public function getExcludedImages($ids): Collection
    {
        return Upload::query()
                    ->whereIn('id', $ids)
                    ->get();
    }
}
