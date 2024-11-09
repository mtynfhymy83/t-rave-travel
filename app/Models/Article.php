<?php

namespace App\Models;

use App\Services\FileUploadService;
use App\Http\Resources\ArticleResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', // عنوان مقاله
        'body', // بدنه مقاله
        'upload_file', // فایل‌های آپلود شده
        'creator', // سازنده مقاله
        'publish', // وضعیت انتشار
        'review', // تعداد بازدید
        'count', // تعداد
        'article_id', // شناسه مقاله (در صورت پاسخ یا زیرمجموعه)
        'parent_id', // شناسه والد مقاله
        'type', // نوع مقاله
        'cover' // تصویر کاور
    ];

    /**
     * رابطه بین مقالات (برای پاسخ‌ها)
     */
    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * رابطه با کامنت‌ها (نظرات) مربوط به مقاله
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * رابطه با کاربری که مقاله را ایجاد کرده
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'creator');
    }

    /**
     * ایجاد مقاله جدید و پردازش فایل‌های آپلود شده
     *
     * @param $user
     * @param Request $request
     * @param FileUploadService $fileUploadService
     * @return Article
     */
    public function createArticle($user, Request $request, FileUploadService $fileUploadService)
    {
        try {
            // اعتبارسنجی و دریافت داده‌ها از درخواست
            $this->title = $request->input('title');
            $this->body = $request->input('body');
            $this->creator = $user->id;

            // پردازش تصویر کاور
            $coverUrl = $request->input('cover');
            $coverUrl = $fileUploadService->moveFileToPermanentStorage($coverUrl); // انتقال فایل به ذخیره‌سازی دائمی
            $this->cover = $coverUrl;

            // پردازش تصاویر داخل بدنه مقاله
            $this->body = $this->processImagesInBody($this->body, $fileUploadService);

            // پردازش فایل‌های پیوست شده
            $uploadedFiles = $this->handleAttachedFiles($request->input('body'), $fileUploadService);
            $this->upload_file = json_encode($uploadedFiles);

            // ذخیره مقاله
            $this->save();

            return $this;
        } catch (\Exception $e) {
            Log::error('Article creation failed: ' . $e->getMessage());
            throw new \Exception('Article creation failed: ' . $e->getMessage());
        }
    }

    /**
     * پردازش تصاویر در بدنه مقاله
     *
     * @param string $bodyHtml
     * @param FileUploadService $fileUploadService
     * @return string
     */
    private function processImagesInBody($bodyHtml, FileUploadService $fileUploadService)
    {
        // پیدا کردن URL تمامی تصاویر در بدنه مقاله
        preg_match_all('/<img[^>]+src="([^">]+)"/i', $bodyHtml, $matches);
        $imageUrls = $matches[1];
        $uploadedFiles = [];
        $imageMap = [];

        // آپلود تصاویر و جایگزینی URL های جدید
        foreach ($imageUrls as $imageUrl) {
            try {
                // انتقال تصویر به ذخیره‌سازی دائمی
                $newUrl = $fileUploadService->moveFileToPermanentStorage($imageUrl);
                $uploadedFiles[] = $newUrl;
                $imageMap[$imageUrl] = $newUrl;
            } catch (\Exception $e) {
                Log::error('Image upload failed for URL: ' . $imageUrl);
            }
        }

        // جایگزینی URL های قدیمی با URL های جدید در بدنه مقاله
        foreach ($imageMap as $oldUrl => $newUrl) {
            $bodyHtml = str_replace($oldUrl, $newUrl, $bodyHtml);
        }

        return $bodyHtml;
    }

    /**
     * پردازش فایل‌های پیوست شده در بدنه مقاله
     *
     * @param string $bodyHtml
     * @param FileUploadService $fileUploadService
     * @return array
     */
    private function handleAttachedFiles($bodyHtml, FileUploadService $fileUploadService)
    {
        // پیدا کردن URL فایل‌های پیوست شده در مقاله
        preg_match_all('/<a[^>]+href="([^">]+)"/i', $bodyHtml, $matches);
        $attachmentUrls = $matches[1];
        $uploadedFiles = [];

        // آپلود فایل‌های پیوست شده و ذخیره URL های جدید
        foreach ($attachmentUrls as $attachmentUrl) {
            try {
                $newUrl = $fileUploadService->moveFileToPermanentStorage($attachmentUrl);
                $uploadedFiles[] = $newUrl;
            } catch (\Exception $e) {
                Log::error('Attachment upload failed for URL: ' . $attachmentUrl);
            }
        }

        return $uploadedFiles;
    }

    /**
     * دریافت تمامی مقالات
     */
    public static function getAllArticles()
    {
        $articles = self::query()->get();
        return ArticleResource::collection($articles);
    }
}
