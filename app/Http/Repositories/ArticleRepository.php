<?php

namespace App\Http\Repositories;

use App\Http\Resources\ArticleResource;
use App\Http\Resources\ProductResource;
use App\Models\Article;
use App\Models\Product;

class ArticleRepository
{


    public static function get6NewestArticles()
    {
        $articles = Article::query()->latest()->take(6)->get();
        return ArticleResource::collection($articles);
    }


    public static function getMostViewedArticles()
    {
        $article = Article::query()
            ->orderBy('review', 'DESC')->paginate(3);
        return ArticleResource::collection($article);
    }

    public static function searchedarticles($search)
    {
        $article = Article::query()->
        where('title', 'like', '%' . $search . '%')->
        orWhere('title_en', 'like', '%' . $search . '%')->
        orWhere('body', 'like', '%' . $search . '%')->paginate(12);
        return ArticleResource::collection($article);
    }

}
