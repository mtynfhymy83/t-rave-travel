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
        $path = $file->store('temp', 'public');
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
        if (!str_contains($filePath, 'public/')) {
            $filePath = 'public/' . $filePath;
        }

        // مسیر کامل فایل
        $localFilePath = storage_path('app/' . $filePath);

        // بررسی اینکه فایل در سیستم وجود دارد
        if (file_exists($localFilePath)) {
            // نام یکتا برای فایل ایجاد می‌کنیم
            $fileName = uniqid() . '.' . pathinfo($localFilePath, PATHINFO_EXTENSION);

            // دریافت محتوای فایل
            $fileContents = file_get_contents($localFilePath);

            // آپلود فایل به فضای دائمی (مثلاً Liara)
            $uploaded = Storage::disk('liara')->put($fileName, $fileContents);

            // در صورت موفقیت، URL فایل در فضای دائمی باز می‌گردد
            return $uploaded ? Storage::disk('liara')->url($fileName) : null;
        }

        return null;
    }
}
