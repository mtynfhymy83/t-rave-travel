<?php

// app/Http/Controllers/Api/V1/ArticleController.php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Services\ArticleService;
use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    protected $articleService;

    // سازنده برای تزریق سرویس ArticleService
    public function __construct(ArticleService $articleService)
    {
        $this->articleService = $articleService;
    }

    // ایجاد مقاله جدید
    public function create(Request $request)
    {
        $user = auth()->user();

        if ($user) {
            $article = $this->articleService->createArticle($request, $user);

            return response()->json([
                'result' => true,
                'message' => "Article created successfully",
                'data' => ['id' => $article->id]
            ], 201);
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }

    // حذف مقاله
    public function remove($id)
    {
        $result = $this->articleService->removeArticle($id);
        if ($result) {
            return response()->json(['message' => 'Article deleted successfully'], 200);
        }

        return response()->json(['message' => 'Article Not Found!'], 404);
    }

    // دریافت جزئیات مقاله
    public function articleDetails($id)
    {
        $article = $this->articleService->getArticleDetails($id);

        if ($article) {
            $article->increment('review');
            return response()->json([
                'result' => true,
                'message' => 'Article details fetched successfully',
                'data' => new ArticleResource($article)
            ], 200);
        }

        return response()->json(['message' => 'Article Not Found!'], 404);
    }

    // جستجو در مقالات
    public function searcharticle(Request $request)
    {
        // گرفتن مقدار جستجو از پارامترهای URL
        $searchTerm = $request->query('search'); // استفاده از query() برای دریافت پارامتر از URL

        // فراخوانی سرویس برای جستجو
        $articles = $this->articleService->searchArticles($searchTerm);

        return response()->json([
            'result' => true,
            'message' => 'Articles fetched successfully',
            'data' => ArticleResource::collection($articles)
        ], 200);
    }

    // ویرایش مقاله
    public function edit(Request $request, $id)
    {
        $article = $this->articleService->editArticle($id, $request->all());

        if ($article) {
            return response()->json([
                'result' => true,
                'message' => "Article updated successfully",
                'data' => new ArticleResource($article)
            ], 200);
        }

        return response()->json(['message' => 'Article Not Found!'], 404);
    }

    // دریافت مقالات کاربر
    public function getUserArticles()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $articles = $this->articleService->getUserArticles($user->id);

        if ($articles->isEmpty()) {
            return response()->json(['message' => 'No articles found.'], 404);
        }

        return response()->json(ArticleResource::collection($articles), 200);
    }
    public function publishArticle(Request $request, $id)
    {
        try {
            // استفاده از سرویس برای انتشار مقاله
            $article = $this->articleService->publishArticle($id, $request);

            // جدا کردن اولین خط از متن body
            $bodyLines = explode("\n", $article->body);
            $bodyFirstLine = $bodyLines[0]; // گرفتن اولین خط

            // بازگشت پاسخ موفق
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
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


}
