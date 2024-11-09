<?php
// app/Http/Controllers/Api/V1/EditorController.php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\FileUploadService;
use Illuminate\Http\Request;

class EditorController extends Controller
{
    protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * Upload a file to local storage.
     */
    public function upload(Request $request)
    {
        // Validate the request
        $request->validate([
            'upload' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('upload')) {
            try {
                $url = $this->fileUploadService->uploadToLocalStorage($request->file('upload'));

                return response()->json([
                    'uploaded' => 1,
                    'url' => $url,
                ]);
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 400);
            }
        }

        return response()->json([
            'error' => 'File not found',
        ], 400);
    }

    /**
     * Move a file from local storage to permanent storage (Liara).
     */
    public function moveFileToPermanentStorage(Request $request)
    {
        try {
            $validated = $request->validate([
                'cover' => 'required|string',
            ]);

            $fileUrl = $validated['cover'];
            $permanentUrl = $this->fileUploadService->moveFileToPermanentStorage($fileUrl);

            return response()->json(['url' => $permanentUrl], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Upload failed: ' . $e->getMessage()], 500);
        }
    }
}
