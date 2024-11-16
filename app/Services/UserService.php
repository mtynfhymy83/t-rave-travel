<?php

// app/Services/UserService.php

namespace App\Services;

use App\Models\User;
use App\DTOs\UserDTO;

class UserService
{
    public function updateUserProfile(User $user, $request)
    {
        // به‌روزرسانی اطلاعات کاربر
        User::updateUserInfo($user, $request);

        // تعیین وضعیت تکمیل پروفایل کاربر
        $user->profile_completed = $user->name && $user->mobile;
        $user->save();

        // بازگشت داده‌ها به صورت DTO
        return new UserDTO(
            $user->id,
            $user->name,
            $user->mobile,
            $user->photo,
            $user->profile_completed
        );
    }

    public function getUserProfile(User $user)
    {
        // بازگشت پروفایل کاربر به صورت DTO
        return new UserDTO(
            $user->id,
            $user->name,
            $user->mobile,
            $user->photo,
            $user->profile_completed
        );
    }
}
