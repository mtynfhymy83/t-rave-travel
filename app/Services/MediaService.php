<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class MediaService
{
    /**
     * ذخیره فایل در فضای موقت
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return string
     */
    public function storeTempFile($file)
    {
        // ذخیره فایل در فضای عمومی به صورت موقت
        $path = $file->store('local', 'public');
        return $path;
    }

    /**
     * انتقال فایل از فضای موقت به فضای دائمی
     *
     * @param string $filePath
     * @return string|null
     */
    public function moveFileToPermanentStorage($filePath)
    {
        // بررسی اینکه فایل در فضای public قرار دارد یا خیر
//        if (!str_contains($filePath, 'public/')) {
//            $filePath = 'public/' . $filePath;
//        }

        // بررسی اینکه فایل در سیستم وجود دارد
        if (Storage::disk('public')->exists($filePath)) {
            // نام یکتا برای فایل ایجاد می‌کنیم
            $fileName = uniqid() . '.' . pathinfo($filePath, PATHINFO_EXTENSION);

            // دریافت محتوای فایل
            $fileContents = Storage::disk('public')->get($filePath);

            // آپلود فایل به فضای دائمی (مثلاً Liara)
            $uploaded = Storage::disk('liara')->put($fileName, $fileContents);

            // در صورت موفقیت، URL فایل در فضای دائمی باز می‌گردد
            if ($uploaded) {
                return Storage::disk('liara')->url($fileName);
            } else {
                return null;
            }
        }

        return false;
    }
}
