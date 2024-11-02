<?php
namespace App\Models;

use App\Http\Controllers\Api\V1\EditorController;
use App\Http\Controllers\Api\V1\ArticleController;
use App\Http\Controllers\Api\V1\MediaController;
use App\Http\Resources\ArticleResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Intervention\Image\ImageManager;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'body',
        'upload_file',
        'creator',
        'publish',
        'review',
        'count',
        'article_id',
        'parent_id',
        'id',


    ];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }

    public function createarticle($user, Request $request)
    {
        $this->cover = $coverUrl = $request->input('cover');
        $this->title = $title = $request->input('title');
        $this->body = $bodyHtml = $request->input('body');




        $coverController = new EditorController();
        $coverResponse = $coverController->moveFileToPermanentStorage(new Request(['image_url' => $coverUrl]));

        if ($coverResponse->getStatusCode() == 200) {
            $coverUrl = json_decode($coverResponse->getContent())->url;
        } else {
            return response()->json(['message' => 'Cover image upload failed.'], 400);
        }


        preg_match_all('/<img[^>]+src="([^">]+)"/i', $bodyHtml, $matches);
        $imageUrls = $matches[1];

        foreach ($imageUrls as $imageUrl) {
            $imageResponse = $coverController->moveFileToPermanentStorage(new Request(['image_url' => $imageUrl]));

            if ($imageResponse->getStatusCode() == 200) {
                $uploadedFiles = json_decode($imageResponse->getContent())->url;
            } else {
                return response()->json(['message' => 'Image upload failed for URL: ' . $imageUrl], 400);
            }
        }
        $user = auth()->user();

        $textContent = preg_replace('/<img[^>]+>/i', '', $bodyHtml);
        $this->body = $textContent;
        $this->cover = $coverUrl;
        $this->id;
        $this->creator = $user->id;
        $this->upload_file = $uploadedFiles;
        $this->save();

        return $this;
    }

    private function uploadImage($imageUrl)
    {
        try {
            // بارگذاری تصویر از URL
            $fileContents = file_get_contents($imageUrl);
            if ($fileContents === false) {
                throw new \Exception('Failed to retrieve image from URL.');
            }

            $fileName = uniqid() . '.' . pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_EXTENSION);
            $uploaded = Storage::disk('liara')->put($fileName, $fileContents);

            if ($uploaded) {
                return ['success' => true, 'url' => Storage::disk('liara')->url($fileName)];
            } else {
                throw new \Exception('File upload to Liara failed.');
            }
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    public static function getAllarticle()
    {
        $article = self::query()->get();
        return ArticleResource::collection($article);
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
