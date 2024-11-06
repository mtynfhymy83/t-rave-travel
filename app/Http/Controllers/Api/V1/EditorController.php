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
            'upload' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);


        // $expireAt = Carbon::now()->addMinutes(10);


        if ($request->hasFile('upload')) {

            $path = $request->file('upload')->store('local', 'public');

            $temporaryUrl = url('storage/' . $path);

            return response()->json([
                'uploaded' => 1,
                'url' => $temporaryUrl,
            ]);
        }


        return response()->json([
            'error' => 'File not found',
        ], 400);
    }

    public function moveFileToPermanentStorage(Request $request)
    {
        try {
            $validated = $request->validate([
                'cover' => 'required|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Upload failed: ' . $e->getMessage()], 400);
        }

        $fileUrl = $validated['cover'];


        $fileName = basename($fileUrl);


        $localFilePath = '/travel/storage/app/public/local/' . $fileName;


        if (file_exists($localFilePath)) {
            $fileContents = file_get_contents($localFilePath);


            $uniqueFileName = uniqid() . '.' . pathinfo($fileName, PATHINFO_EXTENSION);


            $uploaded = Storage::disk('liara')->put($uniqueFileName, $fileContents);

            if ($uploaded) {
                return response()->json(['url' => Storage::disk('liara')->url($uniqueFileName)], 200);
            } else {
                return response()->json(['message' => 'File upload to Liara failed.'], 500);
            }
        } else {
            return response()->json(['message' => 'File not found in local storage.'], 404);
        }
    }
}
