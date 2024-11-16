<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Services\MediaService;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'password',
        'mobile',
        'photo',
        'id',
        'profile_completed',
    ];

    // روابط مدل
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * بروزرسانی اطلاعات کاربر
     *
     * @param \App\Models\User $user
     * @param \Illuminate\Http\Request $request
     */
    public static function updateUserInfo($user, $request)
    {
        // استفاده از سرویس MediaService برای مدیریت فایل‌ها
        $mediaService = new MediaService(); // ایجاد نمونه از سرویس

        // چک کردن اینکه آیا فایلی ارسال شده است یا خیر
        if ($request->string('path')) {
            $filepath = $request->input('path'); // دریافت مقدار فیلد path از درخواست
            $image = $mediaService->moveFileToPermanentStorage($filepath); // ارسال به متد moveFileToPermanentStorage
             // نمایش نتیجه
        } else {
            $image = null;
        }

        // بروزرسانی اطلاعات کاربر
        $user->update([
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'photo' => $image,  // مسیر عکس به‌روز شده

        ]);
    }
}

