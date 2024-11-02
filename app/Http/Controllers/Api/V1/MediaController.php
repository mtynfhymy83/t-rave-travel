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
// اعتبارسنجی ورودی‌ها
        $validated = $request->validate([
            'upload_file' => 'required|file',
        ]);


        $expireAt = Carbon::now()->addMinutes(10);


        $file = $request->file('upload_file');
        if ($file) {

            $filePath = $file->store('local', 'public');

            $temporaryUrl = url('storage/' . $filePath);


            return response()->json([
//                'id' => $id,
                'file_path' => $filePath,
                'file_url' => $temporaryUrl,
                'expire_at' => $expireAt->toDateTimeString(),
            ]);
        }


        return response()->json([
            'error' => 'File not found',
        ], 400);
    }


    public function moveFileToPermanentStorage(Request $request)
    {
        // اعتبارسنجی ورودی‌ها
        try {
            // اعتبارسنجی ورودی‌ها
            $validated = $request->validate([
                'file_path' => 'required|string', // مسیر فایل موقت
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // اگر اعتبارسنجی شکست خورد، پیغام خطا را چاپ کنید
            return response()->json(['message' => 'upload fail ' . $e->getMessage()], 400);
        }

        // دریافت مسیر فایل از ورودی
        $filePath = $validated['file_path'];


        // اگر مسیر شامل 'public/local' نیست، به مسیر اضافه‌اش می‌کنیم
        if (!str_contains($filePath, 'public/')) {
            $filePath = 'public/' . $filePath;
        }

        // مسیر کامل فایل در storage (سیستم محلی)
        $localFilePath = storage_path('app/' . $filePath);

        // بررسی وجود فایل در مسیر محلی
        if (file_exists($localFilePath)) {
            // ساخت یک نام یکتا برای فایل در لیارا
            $fileName = uniqid() . '.' . pathinfo($localFilePath, PATHINFO_EXTENSION);

            // خواندن محتویات فایل محلی
            $fileContents = file_get_contents($localFilePath);

            // آپلود فایل در لیارا
            $uploaded = Storage::disk('liara')->put($fileName, $fileContents);

            // اگر آپلود موفقیت‌آمیز بود
            if ($uploaded) {
                // برگرداندن URL فایل در لیارا
                return Storage::disk('liara')->url($fileName);
            } else {
                return response()->json(['message' => 'File upload to Liara failed.'], 500);
            }
        } else {
            // اگر فایل در سیستم محلی پیدا نشد
            return response()->json(['message' => 'File not found in local storage.'], 404);
        }
    }
}
// نام جدید فایل برای انتقال به دایرکتوری اصلی (public)
//    $newFilePath = str_replace('public', 'liara/', $filePath);
//
//// انتقال فایل به مسیر جدید
//    Storage::move($filePath, $newFilePath);

// پاسخ موفقیت‌آمیز با مسیر جدید فایل
//    return response()->json([
//        'message' => 'File moved successfully',
//        'new_file_path' => $newFilePath,
//        'new_file_url' => Storage::url($newFilePath),
//    ]);
//
//
//return response()->json([
//'error' => 'File not found',
//], 404);
//}
//}
