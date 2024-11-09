<?php

namespace App\Http\Controllers\Api\V1;

use App\DTOs\UserDTO;
use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'result' => false,
                'message' => 'کاربر یافت نشد',
                'data' => []
            ], 403);
        }

        // استفاده از سرویس برای به‌روزرسانی اطلاعات
        $userDTO = $this->userService->updateUserProfile($user, $request);

        return response()->json([
            'result' => true,
            'message' => 'اطلاعات کاربر با موفقیت به‌روزرسانی شد',
            'data' => [
                'user' => $userDTO
            ]
        ], 200);
    }

    public function profile(Request $request)
    {
        $user = auth()->user();

        // استفاده از سرویس برای دریافت پروفایل کاربر
        $userDTO = $this->userService->getUserProfile($user);

        return response()->json([
            'result' => true,
            'message' => 'پروفایل کاربر',
            'data' => [
                'user' => $userDTO
            ]
        ], 200);
    }
}
