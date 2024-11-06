<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Http\Request;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

use App\Http\Controllers\Api\V1;
class MediaController extends Controller
{
    public function tempMedia(Request $request)
    {

        $validated = $request->validate([
            'upload' => 'required|file',
        ]);


        if ($request->hasFile('upload')) {

            $path = $request->file('upload')->store('local', 'public');
            $temporaryUrl = url('storage/' . $path);

            return response()->json([
                'uploaded' => 1,
                'url' => $temporaryUrl,
                'path' => $path,

            ]);
        }
        // if ($file) {

        // $filePath = $file->store('local', 'public');

        // $temporaryUrl = url('storage/' . $filePath);


//             return response()->json([
// //                'id' => $id,
//                 'url' => $temporaryUrl,
//                 'uploaded' => 1,

//             ]);
        // }


        return response()->json([
            'error' => 'File not found',
        ], 400);
    }


    public function moveFileToPermanentStorage(Request $request)
    {

        try {

            $validated = $request->validate([
                'path' => 'required|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json(['message' => 'upload fail ' . $e->getMessage()], 400);
        }


        $filePath = $validated['path'];


        if (!str_contains($filePath, 'public/')) {
            $filePath = 'public/' . $filePath;
        }


        $localFilePath = storage_path('app/' . $filePath);


        if (file_exists($localFilePath)) {

            $fileName = uniqid() . '.' . pathinfo($localFilePath, PATHINFO_EXTENSION);


            $fileContents = file_get_contents($localFilePath);


            $uploaded = Storage::disk('liara')->put($fileName, $fileContents);


            if ($uploaded) {

                return Storage::disk('liara')->url($fileName);
            } else {
                return response()->json(['message' => 'File upload to Liara failed.'], 500);
            }
        } else {

            return response()->json(['message' => 'File not found in local storage.'], 404);
        }
    }

}
