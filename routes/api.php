<?php


use App\Http\Controllers\Api\V1\ReplyController;
use App\Http\Controllers\Api\V1\TicketController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Kernel;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix('/v1')->namespace('Api\V1')->group(function () {
    Route::middleware('auth:sanctum')->get('/get-user-info', function (Request $request) {

    });

    Route::post('send_sms', [\App\Http\Controllers\Api\V1\AuthApiController::class, 'sendSms']);
    Route::post('verify_sms_code', [\App\Http\Controllers\Api\V1\AuthApiController::class, 'verifySms']);
    Route::get('home', [\App\Http\Controllers\Api\V1\HomeApiController::class, 'home']);
    Route::middleware('auth:sanctum')->post('update_profile', [\App\Http\Controllers\Api\V1\UserController::class, 'updateprofile']);
    Route::get('article_details/{id}', [\App\Http\Controllers\Api\V1\ArticleController::class, 'articleDetails']);
    Route::middleware('auth:sanctum')->post('save_article_comment',[\App\Http\Controllers\Api\V1\ArticleController::class, 'saveComment']);
    Route::get('search_article',[\App\Http\Controllers\Api\V1\ArticleController::class, 'searcharticle']);
    Route::post('tickets/{ticket}/reply', [\App\Http\Controllers\Api\V1\ReplyController::class, 'replyToTicket']);
    Route::post('comments/{comment}/reply', [ReplyController::class, 'replyToComment']);
    Route::post('temp_media', [App\Http\Controllers\Api\V1\MediaController::class, 'tempMedia']);



});

Route::prefix('/v1')->namespace('Api\V1')->middleware('auth:sanctum')->group(function (){
    Route::get('profile', [\App\Http\Controllers\Api\V1\UserController::class, 'profile']);
    Route::middleware(['auth:sanctum' , 'profile'])->post('tickets', [\App\Http\Controllers\Api\V1\TicketApicontroller::class, 'create']);
    Route::middleware(['auth:sanctum' , 'profile'])->get('show_ticket', [\App\Http\Controllers\Api\V1\TicketApicontroller::class, 'getAll']);
    Route::middleware(['auth:sanctum' , 'profile'])->get('get/ticket/{id}', [\App\Http\Controllers\Api\V1\TicketApicontroller::class, 'getticket']);
    Route::middleware(['auth:sanctum' , 'profile'])->post('article' , [\App\Http\Controllers\Api\V1\ArticleController::class, 'create']);
    Route::middleware(['auth:sanctum' , 'profile'])->post('remove/{id}' , [\App\Http\Controllers\Api\V1\ArticleController::class, 'remove']);
    Route::middleware(['auth:sanctum' , 'profile'])->post('article/{id}/publish' , [\App\Http\Controllers\Api\V1\ArticleController::class, 'publishArticle']);
    Route::middleware(['auth:sanctum' , 'profile'])->get('getall_article' , [\App\Http\Controllers\Api\V1\ArticleController::class, 'getUserArticles']);
    Route::middleware(['auth:sanctum' , 'profile'])->post('message' , [\App\Http\Controllers\Api\V1\MessageController::class, 'sendMessageWithAttachment']);
    Route::middleware(['auth:sanctum' , 'profile'])->get('messages/{ticketId}' , [\App\Http\Controllers\Api\V1\MessageController::class, 'getMessagesForTicket']);
});


