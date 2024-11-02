<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EditorController extends Controller
{
    public function upload(Request $request)
    {
// Validate the request
        $request->validate([
            'upload_file' => 'required|file',
        ]);

// Store the image
        if ($request->hasFile('upload_file')) {
            $path = $request->file('upload_file')->store('images', 'public');

// Generate the URL for the uploaded image
            $url = Storage::url($path);

// Return the response in the format CKEditor expects
            return response()->json([
                'uploaded' => 1,
                'url' => $url,
            ]);
        }

        return response()->json(['uploaded' => 0], 400);
    }

    public function moveFileToPermanentStorage(Request $request)
    {
        // اعتبارسنجی ورودی‌ها
        $validated = $request->validate([
            'image_url' => 'required|url',
        ]);

        $imageUrl = $validated['image_url'];
        $fileContents = null;

        // بررسی اینکه URL محلی است یا خارجی
        if (str_contains($imageUrl, 'http://127.0.0.1:8000/storage/local/')) {
            $localPath = storage_path('app/public/local/' . basename($imageUrl));

            // بررسی وجود فایل
            if (file_exists($localPath)) {
                // خواندن محتویات فایل
                $fileContents = file_get_contents($localPath);

                // ساخت نام یکتا برای فایل
                $fileName = uniqid() . '.' . pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_EXTENSION);

                // آپلود فایل به لیارا
                $uploaded = Storage::disk('liara')->put($fileName, $fileContents);

                // بررسی نتیجه آپلود
                if ($uploaded) {
                    return response()->json(['url' => Storage::disk('liara')->url($fileName)], 200);
                } else {
                    return response()->json(['message' => 'File upload to Liara failed.'], 500);
                }
            } else {
                return response()->json(['message' => 'Local file not found.'], 404);
            }
        } else {
            return response()->json(['message' => 'Invalid image URL.'], 400);
        }
    }
}
