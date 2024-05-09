<?php

namespace App\Http\Controllers;

use App\Models\Upload;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadUploadController extends Controller
{
    public function download(Upload $upload)
    {
        $upload = Upload::findOrFail($upload->id);
        $path = $upload->imagePath;  // Ensure your upload model has the correct path stored

        if (!Storage::exists($path)) {
            abort(404);
        }

        $response = new StreamedResponse(function() use ($path) {
            $stream = Storage::readStream($path);
            while (!feof($stream)) {
                echo fread($stream, 2048);
            }
            fclose($stream);
        });

        $response->headers->set('Content-Type', Storage::mimeType($path));
        $response->headers->set('Content-Disposition', 'attachment; filename="' . basename($path) . '"');
        $response->headers->set('Content-Length', Storage::size($path));

        return $response;
    }
}
