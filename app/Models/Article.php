<?php
namespace App\Models;

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
        'parent_id'


    ];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }
    public static function createarticle($user, $request)
    {

        if ($request->hasFile('upload_file')) {
            $mediaController = new MediaController();
            $image = $mediaController->moveFileToPermanentStorage($request);
        } else {
            $image = 'nothing to upload';
        }
          return  self::create([
            'title'=>$request->input('title'),
            'body'=>$request->input('body'),
            'upload_file'=>$image,
              'creator'=>$user->id
        ]);

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
