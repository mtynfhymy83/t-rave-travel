<?php


namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthApiController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function sendSms(Request $request)
    {
        $mobile = $request->input('mobile');
        $response = $this->authService->sendSmsCode($mobile);

        return response()->json($response, $response['result'] ? 201 : 403);
    }

    public function verifySms(Request $request)
    {
        $mobile = $request->input('mobile');
        $code = $request->input('code');

        $response = $this->authService->verifySmsCode($mobile, $code);

        return response()->json($response, $response['result'] ? 201 : 403);
    }
}
