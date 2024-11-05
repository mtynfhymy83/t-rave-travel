<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Repositories\ArticleRepository;
use App\Http\Resources\CommentResource;
use App\Http\Repositories\ProductRepository;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\UserResource;
use App\Http\services\Keys;
use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'cover' => 'required|string',
        ]);

        $user = auth()->user();

        if ($user) {

            $article = new Article();
            $result = $article->createarticle($user, $request);
            return response()->json([
                'result' => true,
                'message' => "Article created successfully",
                'data' => ['id' => $result->id]
            ], 201);
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }

//Keys::articles => new ArticleResource($result)

    public function remove($id)
    {
        $article = Article::find($id);
        if (!$article) {
            return response()->json(['message' => 'Article Not Found!'], 404);
        }
        $article->delete();
        return response()->json($article);
    }

    public function articleDetails($id)
    {
        $article = Article::query()->find($id);
        if($article) {

            $article->increment('review');
            $articleResource = new ArticleResource($article);
        } else { return response()->json(['message' => 'Article Not Found!'], 404); }
        return response()->json([
            'result' => true,
            'message' => 'application products page',
            'data' => [
                new ArticleResource($article)
            ]

        ], 200);
    }
    public function saveComment(Request $request)
    {

        $user = auth()->user();
     Comment::query()->create([
            'body'=>$request->input('body'),
            'parent_id'=>$request->input('parent_id',null),
            'user_id'=>$user->id,
            'article_id'=>$request->input('article_id'),

        ]);

        $article = Article::query()->find($request->input('article_id'));

        return response()->json([
            'result'=>true,
            'message'=>'application products page',
            'data'=>[
                new ArticleResource($article)
            ]

        ],200);
    }
    public function searcharticle(Request $request)
    {
        return response()->json([
            'result'=>true,
            'message'=>'application products page',
            'data'=>[
//                Keys::articles=>Article::getAllarticle(),
                Keys::searched_article=>ArticleRepository::searchedarticles($request->input('search'))->response()->getData(true),
            ]

        ],200);
    }
    public function publishArticle(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|string',
            'publish_hour' => 'required|string',
            'publish_date' => 'required|string',
        ]);

        $article = Article::findOrFail($id);
        $article->type = $request->input('type');
        $article->publish_hour = $request->input('publish_hour');
        $article->publish_date = $request->input('publish_date');
        $article->publish =  1;
        $article->save();

        return response()->json([
            'result' => true,
            'message' => "Article published successfully",
            'data' => [
                'title' => $article->title,
                'body' => $article->body,
                'type' => $article->type,
                'publish_hour' => $article->publish_hour,
                'publish_date' => $article->publish_date,
                'publish' => $article->publish,
                'cover' => $article->cover,
                'comments'=>CommentResource::collection($article->comments),

            ]
        ], 200);
    }
    public function edit(Request $request, $id)
    {
        // اعتبارسنجی ورودی
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'cover' => 'string|url', // پوشش اختیاری
            'publish_date' => 'required|date',
            'publish_hour' => 'required|date_format:H:i',
            'images' => 'array', // آرایه‌ای از URLها برای تصاویر
            'images.*' => 'string|url' // هر URL در آرایه باید یک URL معتبر باشد
        ]);

        // یافتن مقاله
        $article = Article::find($id);
        if (!$article) {
            return response()->json(['message' => 'Article Not Found!'], 404);
        }

        // به‌روزرسانی ویژگی‌ها
        $article->title = $request->input('title');
        $article->body = $request->input('body');

        // اگر پوشش جدیدی ارائه شده باشد، آن را به‌روزرسانی کنید
        if ($request->has('cover')) {
            // بارگذاری دوباره تصویر کاور
            $coverController = new EditorController();
            $coverResponse = $coverController->moveFileToPermanentStorage(new Request(['image_url' => $request->input('cover')]));

            if ($coverResponse->getStatusCode() == 200) {
                $article->cover = json_decode($coverResponse->getContent())->url;
            } else {
                return response()->json(['message' => 'Cover image upload failed.'], 500);
            }
        }

        // بارگذاری دوباره تصاویر
        if ($request->has('images')) {
            $coverController = new EditorController();
            $uploadedFiles = [];

            foreach ($request->input('images') as $imageUrl) {
                $imageResponse = $coverController->moveFileToPermanentStorage(new Request(['image_url' => $imageUrl]));

                if ($imageResponse->getStatusCode() == 200) {
                    $uploadedFiles[] = json_decode($imageResponse->getContent())->url;
                } else {
                    Log::error('Image upload failed for URL: ' . $imageUrl);
                }
            }

            // ذخیره URLهای جدید تصاویر
            $article->upload_file = json_encode($uploadedFiles);
        }

        // به‌روزرسانی تاریخ انتشار
        $article->publish_date = $request->input('publish_date');
        $article->publish_hour = $request->input('publish_hour');
        $article->publish = true; // یا بسته به منطق شما

        // ذخیره تغییرات
        $article->save();

        return response()->json([
            'result' => true,
            'message' => "Article updated successfully",
            'data' => new ArticleResource($article)
        ], 200);
    }
    public function getUserArticles()
    {
        // دریافت کاربر جاری
        $user = Auth::user();

        // بررسی اینکه آیا کاربر وارد شده است
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // دریافت مقالات منتشر شده توسط کاربر
        $articles = Article::where('creator', $user->id) // فرض بر اینکه user_id در جدول articles وجود دارد
        ->where('publish', 1) // فرض بر اینکه فیلدی برای نشان دادن وضعیت انتشار وجود دارد
        ->get();

        // بررسی اینکه آیا مقاله‌ای پیدا شده است یا خیر
        if ($articles->isEmpty()) {
            return response()->json(['message' => 'No articles found.'], 404);
        }

        return response()->json($articles, 200);
    }
}

