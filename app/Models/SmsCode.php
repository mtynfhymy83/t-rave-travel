<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use App\Http\Controllers\Api\V1\AuthApiController;

class SmsCode extends Model
{
    use HasFactory;

    protected Client $client;
    protected mixed $apiKey;
    protected string $apiUrl;


    protected $fillable = [
        'mobile',
        'code'
    ];

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = env('KAVEHNEGAR_API_KEY'); // تعریف کلید API در فایل .env
        $this->apiUrl = 'https://api.kavenegar.com/v1/' . $this->apiKey . '/send.json';
    }

    public static function checkTwoMinute($mobile)
    {
        $check = self::query()->where('mobile', $mobile)
            ->where('created_at', '>', Carbon::now()->subMinute(2))->first();
        if ($check) {
            return true;
        }
        return false;

    }

    /**
     * @throws GuzzleException
     */
    public static function createSmsCode($mobile)
    {
        $code = rand(1111, 9999);
        $client = new Client(); // ایجاد یک نمونه جدید
        $apiKey = env('KAVEHNEGAR_API_KEY'); // کلید API

        $response = $client->post('https://api.kavenegar.com/v1/' . $apiKey . '/sms/send.json', [
            'form_params' => [
                'receptor' => $mobile,
                'message' => $code,
            ]
        ]);

        // ذخیره کد در پایگاه داده
        self::create([
            'mobile' => $mobile,
            'code' => $code,
        ]);

        return json_decode($response->getBody(), true);
    }

    public static function checkSend($mobile, $code)
    {
//
//        {
//            return true;
//        }
        $check = self::query()->where([
            'mobile' => $mobile,
            'code' => $code
        ])->first();

        if ($check) {
            return true;
        }
        return false;
    }
}


