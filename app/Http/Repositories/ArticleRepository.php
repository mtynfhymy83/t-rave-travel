<?php

namespace App\Http\Repositories;

use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;


class ArticleRepository
{


    public static function get6NewestArticles()
    {
        $articles = Article::query()->latest()->take(6)->get();

        // تنها اولین خط از تگ <p> از هر مقاله را نمایش می‌دهیم
        $articlesWithFirstLine = $articles->map(function ($article) {
            // استخراج محتوای تگ <p> با استفاده از regex
            preg_match('/<p[^>]*>(.*?)<\/p>/', $article->body, $matches);

            // اگر تگ <p> پیدا شد، اولین خط آن را از body بگیرید
            if (isset($matches[1])) {
                $article->body = strtok($matches[1], "\n"); // فقط اولین خط از متن تگ <p>
            } else {
                $article->body = ''; // اگر تگ <p> پیدا نشد، مقدار body خالی شود
            }

            return $article;
        });

        return ArticleResource::collection($articlesWithFirstLine);
    }



    public static function getMostViewedArticles()
    {
        $articles = Article::query()
            ->orderBy('review', 'DESC')
            ->paginate(3);

        // تنها اولین خط از تگ <p> از هر مقاله را نمایش می‌دهیم
        $articlesWithFirstLine = $articles->getCollection()->map(function ($article) {
            // استخراج محتوای تگ <p> با استفاده از regex
            preg_match('/<p[^>]*>(.*?)<\/p>/', $article->body, $matches);

            // اگر تگ <p> پیدا شد، اولین خط آن را از body بگیرید
            if (isset($matches[1])) {
                $article->body = strtok($matches[1], "\n"); // فقط اولین خط از متن تگ <p>
            } else {
                $article->body = ''; // اگر تگ <p> پیدا نشد، مقدار body خالی شود
            }

            return $article;
        });

        // تنظیم مقالات با فقط اولین خط از body
        $articles->setCollection($articlesWithFirstLine);

        return ArticleResource::collection($articles);
    }




    public function create(array $data)
    {
        return Article::create($data);
    }

    // دریافت تمامی مقالات منتشر شده
    public function getAll()
    {
        return Article::where('publish', 1)->get();
    }

    // دریافت مقاله بر اساس ID
    public function getById($id)
    {
        return Article::findOrFail($id);
    }

    // جستجوی مقالات
    public function searchArticles($searchTerm)
    {
        $articles = Article::query()
            ->where('title', 'like', '%' . $searchTerm . '%')
            ->orWhere('title_en', 'like', '%' . $searchTerm . '%')
            ->orWhere('body', 'like', '%' .$searchTerm . '%')
            ->paginate(12);

        // تنها اولین خط از تگ <p> از هر مقاله را نمایش می‌دهیم
        $articlesWithFirstLine = $articles->getCollection()->map(function ($article) {
            // استخراج محتوای تگ <p> با استفاده از regex
            preg_match('/<p[^>]*>(.*?)<\/p>/', $article->body, $matches);

            // اگر تگ <p> پیدا شد، اولین خط آن را از body بگیرید
            if (isset($matches[1])) {
                $article->body = strtok($matches[1], "\n"); // فقط اولین خط از متن تگ <p>
            } else {
                $article->body = ''; // اگر تگ <p> پیدا نشد، مقدار body خالی شود
            }

            return $article;
        });

        // بازگشت مقالات با فقط اولین خط از body
        $articles->setCollection($articlesWithFirstLine);

        return ArticleResource::collection($articles);
    }


    // ویرایش مقاله
    public function update($id, array $data)
    {
        $article = Article::find($id);
        if ($article) {
            $article->update($data);
            return $article;
        }
        return null;
    }

    // حذف مقاله
    public function delete($id)
    {
        $article = Article::find($id);
        if ($article) {
            return $article->delete();
        }
        return false;
    }

    // دریافت مقالات توسط کاربر
    public function getArticlesByUser($userId)
    {
        return Article::where('creator', $userId)
            ->where('publish', 1)
            ->get();
    }


}
