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
        'type',


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
        try {
            $this->title = $request->input('title');
//            $this->type = $request->input('type');

            $bodyHtml = $request->input('body');
            $coverUrl = $request->input('cover');


            $coverController = new EditorController();
            $coverResponse = $coverController->moveFileToPermanentStorage(new Request(['cover' => $coverUrl]));


            if ($coverResponse->getStatusCode() == 200) {
                $coverUrl = json_decode($coverResponse->getContent())->url;
            } else {
                throw new \Exception('Cover image upload failed.');
            }


            preg_match_all('/<img[^>]+src="([^">]+)"/i', $bodyHtml, $matches);
            $imageUrls = $matches[1];
            $uploadedFiles = [];
            $imageMap = [];

            foreach ($imageUrls as $imageUrl) {
                $imageResponse = $coverController->moveFileToPermanentStorage(new Request(['cover' => $imageUrl]));

                if ($imageResponse->getStatusCode() == 200) {
                    $newUrl = json_decode($imageResponse->getContent())->url;
                    $uploadedFiles[] = $newUrl;
                    $imageMap[$imageUrl] = $newUrl;
                } else {
                    Log::error('Image upload failed for URL: ' . $imageUrl);
                }
            }


            foreach ($imageMap as $oldUrl => $newUrl) {
                $bodyHtml = str_replace($oldUrl, $newUrl, $bodyHtml);
            }


            $textContent = preg_replace('/<img[^>]+>/i', '', $bodyHtml);

            $this->body = $bodyHtml;
            $this->cover = $coverUrl;
            $this->creator = $user->id;
            $this->upload_file = json_encode($uploadedFiles);
            $this->save();
return $this;


        } catch (\Exception $e) {
            Log::error('Article creation failed: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
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
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
