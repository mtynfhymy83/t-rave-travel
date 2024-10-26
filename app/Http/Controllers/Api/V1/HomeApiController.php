<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use App\Http\Repositories\ArticleRepository;
use App\Http\services\Keys;
use App\Models\Article;
use App\Models\Category;
use App\Models\Slider;
use Illuminate\Http\Request;

class HomeApiController extends Controller
{

    public function home()
    {
        return response()->json([
            'result'=>true,
            'message'=>'application home page',
            'data'=>[
                Keys::most_viewed_article=>ArticleRepository::getMostViewedArticles(),
                Keys::newest_article=>ArticleRepository::get6NewestArticles()
            ]

        ],200);
    }
}
