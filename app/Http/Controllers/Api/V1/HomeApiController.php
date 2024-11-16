<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Repositories\ArticleRepository;
use App\Services\Keys;

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
