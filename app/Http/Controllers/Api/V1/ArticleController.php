<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Repositories\ArticleRepository;
use App\Http\Repositories\ProductRepository;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\UserResource;
use App\Http\services\Keys;
use App\Models\Article;
use App\Models\Brand;
use App\Models\Comment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'body' => 'required|string',
            'upload_file' => 'required|image'
        ]);

//        $filePath = $request->file('cover')->store('covers', 'public');
        $user = auth()->user();

        if ($user) {

            $article = Article::createarticle($user, $request);
            return Response()->json([
                'result' => true,
                'message' => "article created successfully",
                'data' => [
                    Keys::articles => new ArticleResource($article)
                ]
            ], 201);

//        return response()->json($article->load('creator:id,name'), 201);
        }
    }


    public function getAll()
    {
        $articles = Article::with('creator:id,name')->orderBy('id', 'desc')->get();
        return response()->json($articles);
    }

    public function getOne($shortName)
    {
        $article = Article::where('shortName', $shortName)->with(['category', 'creator:id,name'])->first();
        return response()->json($article);
    }

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
}
