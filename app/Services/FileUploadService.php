<?php

// app/Services/FileUploadService.php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class FileUploadService
{
    /**
     * Upload file to local storage and return URL.
     *
     * @param string $fileInput
     * @return string
     */
    public function uploadToLocalStorage($fileInput): string
    {
        if ($fileInput) {
            // Store file in public disk (local storage)
            $path = $fileInput->store('local', 'public');
            $url = url('storage/' . $path);
            return $url;
        }

        throw new \Exception('No file uploaded.');
    }

    /**
     * Move file to permanent storage (Liara) and return the URL.
     *
     * @param string $fileUrl
     * @return string
     */
    public function moveFileToPermanentStorage($coverUrl)
    {
        try {
            $fileName = basename($coverUrl);
            $localFilePath = storage_path('app/public/local/' . $fileName);

            if (file_exists($localFilePath)) {
                $fileContents = file_get_contents($localFilePath);
                $uniqueFileName = uniqid() . '.' . pathinfo($fileName, PATHINFO_EXTENSION);

                // Upload to Liara storage
                $uploaded = Storage::disk('liara')->put($uniqueFileName, $fileContents);

                if ($uploaded) {
                    return Storage::disk('liara')->url($uniqueFileName);
                } else {
                    throw new \Exception('File upload to Liara failed.');
                }
            } else {
                throw new \Exception('File not found in local storage.');
            }
        } catch (\Exception $e) {
            Log::error('File upload failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
