<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Service\UploadService;

class ExcludedImageController extends Controller
{
    protected UploadService $uploadService;

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    public function index(Device $device)
    {
        $ids = json_decode($device->upload_ids, true) ?? [];

        return $this->uploadService->getExcludedImages($ids);
    }
}
