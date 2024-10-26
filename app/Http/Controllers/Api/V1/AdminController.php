<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Authenticatable;

class AdminController extends Controller
{
    use Authenticatable;
    protected $redirectTo = '/home';
    public function __construct()
    {
        $this->middleware('guest:web');
        $this->middleware('guest:admin');
    }
    public function showLoginForm(){
        return ;
    }




}
