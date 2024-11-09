<?php

// app/Services/AuthService.php

namespace App\services;

use App\Models\SmsCode;
use App\Models\User;

class AuthService
{
    public function sendSmsCode($mobile)
    {
        // بررسی ارسال پیامک در دو دقیقه گذشته
        $checkLastSms = SmsCode::checkTwoMinute($mobile);
        if (!$checkLastSms) {
            // ارسال کد پیامک جدید
            SmsCode::createSmsCode($mobile);
            return [
                'result' => true,
                'message' => 'پیام با موفقیت ارسال شد',
                'data' => ['mobile' => $mobile]
            ];
        } else {
            return [
                'result' => false,
                'message' => 'لطفاً دو دقیقه صبر کنید',
                'data' => []
            ];
        }
    }

    public function verifySmsCode($mobile, $code)
    {
        // بررسی کد پیامک ارسال شده
        $check = SmsCode::checkSend($mobile, $code);
        if ($check) {
            // اگر کاربر قبلاً ثبت‌نام کرده باشد
            $user = User::query()->where('mobile', $mobile)->first();

            if ($user) {
                $token = $user->createToken('my-app-token')->plainTextToken;
                return [
                    'result' => true,
                    'message' => 'کاربر قبلاً ثبت‌نام کرده است',
                    'data' => [
                        'id' => $user->id,
                        'token' => $token
                    ]
                ];
            } else {
                try {
                    // ایجاد کاربر جدید
                    $user = User::create([
                        'mobile' => $mobile,
                        // می‌توان رمز عبور به طور پیش‌فرض ایجاد کرد
                    ]);

                    // چک کردن موفقیت‌آمیز بودن ایجاد کاربر
                    if ($user) {
                        return [
                            'result' => true,
                            'message' => 'کاربر جدید ایجاد شد',
                            'data' => [
                                'id' => $user->id,
                                'token' => $user->createToken('new Token')->plainTextToken
                            ]
                        ];
                    } else {
                        return [
                            'result' => false,
                            'message' => 'ایجاد کاربر با شکست مواجه شد',
                            'data' => []
                        ];
                    }
                } catch (\Exception $e) {
                    return [
                        'result' => false,
                        'message' => $e->getMessage(),
                        'data' => []
                    ];
                }
            }
        }

        return [
            'result' => false,
            'message' => 'کد وارد شده اشتباه است',
            'data' => []
        ];
    }
}
