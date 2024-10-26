<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Repositories\UserRepository;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\UserResource;
use App\Http\services\Keys;
use App\Models\User;
use http\Env\Response;
use Illuminate\Http\Request;

class UserController extends Controller
{


    public static function updateprofile(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'result' => false,
                'message' => "User not found",
                'data' => []
            ], 403);
        }

        // به‌روزرسانی اطلاعات کاربر
        User::updateUserInfo($user, $request);

        // بررسی کامل بودن پروفایل
        if ($user->name && $user->mobile) {
            $user->profile_completed = true;
        } else {
            $user->profile_completed = false; // در صورت عدم وجود نام یا عکس، پروفایل کامل نیست
        }

        $user->save();

        return response()->json([
            'result' => true,
            'message' => "User updated",
            'data' => [
                Keys::user => new UserResource($user)
            ]
        ], 200); // تغییر کد وضعیت به 200 برای موفقیت
    }




    public function profile(Request $request)
    {
        $user = auth()->user();
        return Response()->json([
            'result'=>true,
            'message'=>"user profile",
            'data'=>[
                Keys::user=> new UserResource($user),

            ]
        ],200);
    }



}
