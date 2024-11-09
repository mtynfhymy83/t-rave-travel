<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\MediaService; // سرویس جدید
use Illuminate\Http\Request;

class MediaController extends Controller
{
    protected $mediaService;

    public function __construct(MediaService $mediaService)
    {
        // تزریق سرویس به کنترلر
        $this->mediaService = $mediaService;
    }

    /**
     * ذخیره فایل به صورت موقت و بازگشت آدرس موقت
     */
    public function tempMedia(Request $request)
    {
        // اعتبارسنجی فایل دریافتی
        $validated = $request->validate([
            'upload' => 'required|file',
        ]);

        // بررسی اینکه آیا فایل ارسال شده است یا خیر
        if ($request->hasFile('upload')) {
            // ذخیره فایل در فضای موقت
            $path = $this->mediaService->storeTempFile($request->file('upload'));
            $temporaryUrl = url('storage/' . $path);

            // بازگشت اطلاعات فایل
            return response()->json([
                'uploaded' => 1,
                'url' => $temporaryUrl,
                'path' => $path,
            ]);
        }

        // اگر فایلی یافت نشد، ارسال پیام خطا
        return response()->json([
            'error' => 'File not found',
        ], 400);
    }

    /**
     * انتقال فایل از فضای موقت به فضای دائمی
     */
    public function moveFileToPermanentStorage(Request $request)
    {
        // اعتبارسنجی مسیر فایل
        $validated = $request->validate([
            'path' => 'required|string',
        ]);

        // دریافت مسیر فایل
        $filePath = $validated['path'];

        // انتقال فایل به فضای دائمی
        $url = $this->mediaService->moveFileToPermanentStorage($filePath);

        // بررسی نتیجه انتقال
        if ($url) {
            return response()->json([
                'url' => $url,
                'message' => 'File uploaded successfully.',
            ]);
        }

        // اگر فایل پیدا نشد یا آپلود به مشکل خورد
        return response()->json([
            'message' => 'File upload failed.',
        ], 500);
    }
}
