<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class S3Controller extends Controller
{
    public function showUserInterface()
    {
        return view('userinterface');
    }

    public function uploadFile(Request $request)
    {
        $request->validate([
            'upload_file' => 'required|max:2048', // Adjust the file size validation as needed
        ]);

        $file = $request->file('upload_file');
        $fileName = $file->getClientOriginalName();

        $uploaded = Storage::disk('liara')->put($fileName, file_get_contents($file));

        if($uploaded)
            return true;
        else
            return false;

    }

    public function showObjects()
    {
        $objects = Storage::disk('liara')->allFiles('');

        $files = [];
        foreach ($objects as $object) {
            $files[] = [
//                'name' => $object,
                'download_link' => Storage::disk('liara')->Url($object),
            ];
        }

        return  ['files' => $files];
    }


    public function downloadFile(Request $request)
    {
        $fileName = $request->input('upload_file');
        return Storage::disk('liara')->download($fileName);
    }

    public function deleteFile(Request $request)
    {
        $fileName = $request->input('delete_file');
        Storage::disk('liara')->delete($fileName);

        return redirect()->route('user.interface')->with('success', 'File deleted successfully');
    }
}
