<?php

namespace App\Http\Controllers\Api\V1;

use App\DTOs\UserDTO;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public static function updateprofile(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'result' => false,
                'message' => "کاربر یافت نشد",
                'data' => []
            ], 403);
        }


        User::updateUserInfo($user, $request);


        if ($user->name && $user->mobile) {
            $user->profile_completed = true;
        } else {
            $user->profile_completed = false;
        }

        $user->save();


        $userDTO = new UserDTO(
            $user->id,
            $user->name,
            $user->mobile,
            $user->photo,
            $user->profile_completed
        );

        return response()->json([
            'result' => true,
            'message' => "اطلاعات کاربر با موفقیت به‌روزرسانی شد",
            'data' => [
                'user' => $userDTO
            ]
        ], 200);
    }

    public function profile(Request $request)
    {
        $user = auth()->user();


        $userDTO = new UserDTO(
            $user->id,
            $user->name,
            $user->mobile,
            $user->photo,
            $user->profile_completed
        );

        return response()->json([
            'result' => true,
            'message' => "پروفایل کاربر",
            'data' => [
                'user' => $userDTO
            ]
        ], 200);
    }
}
