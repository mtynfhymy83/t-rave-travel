<?php

// app/Services/ArticleService.php

namespace App\Services;

use App\Http\Repositories\ArticleRepository;
use Illuminate\Http\Request;

class ArticleService
{
    protected $articleRepository;

    // تزریق مخزن ArticleRepository
    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    // ایجاد مقاله جدید
    public function createArticle(Request $request, $user)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'cover' => 'required|string',
        ]);

        $data = [
            'title' => $validatedData['title'],
            'body' => $validatedData['body'],
            'cover' => $validatedData['cover'],
            'creator' => $user->id,
            'publish' => 0, // مقاله به طور پیش فرض منتشر نمی‌شود
        ];

        return $this->articleRepository->create($data);
    }

    // حذف مقاله
    public function removeArticle($id)
    {
        return $this->articleRepository->delete($id);
    }

    // دریافت جزئیات مقاله
    public function getArticleDetails($id)
    {
        return $this->articleRepository->getById($id);
    }

    // جستجو در مقالات
    public function searchArticles($searchTerm)
    {
        return $this->articleRepository->searchArticles($searchTerm);
    }

    // ویرایش مقاله
    public function editArticle($id, array $data)
    {
        return $this->articleRepository->update($id, $data);
    }

    // دریافت مقالات کاربر
    public function getUserArticles($userId)
    {
        return $this->articleRepository->getArticlesByUser($userId);
    }
    public function publishArticle($id, Request $request)
    {
        // اعتبارسنجی ورودی
        $request->validate([
            'type' => 'required|string',
            'publish_hour' => 'required|string',
            'publish_date' => 'required|string',
        ]);

        // گرفتن مقاله از ریپازیتوری
        $article = $this->articleRepository->getById($id);

        if (!$article) {
            throw new \Exception("Article not found.");
        }

        // بروزرسانی اطلاعات مقاله
        $article->type = $request->input('type');
        $article->publish_hour = $request->input('publish_hour');
        $article->publish_date = $request->input('publish_date');
        $article->publish = 1; // منتشر شده
        $article->save();

        return $article;
    }

}

