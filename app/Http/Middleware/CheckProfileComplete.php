<?php
namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckProfileComplete
{
/**
* Handle an incoming request.
*
* @param  \Illuminate\Http\Request  $request
* @param  \Closure  $next
* @return mixed
*/
public function handle(Request $request, Closure $next)
{
$user = Auth::user();

// فرض کنید که فیلد 'profile_completed' برای بررسی کامل بودن پروفایل کاربر وجود دارد
if ($user && !$user->profile_completed) {
// اگر پروفایل کاربر کامل نیست، او را به صفحه ویرایش پروفایل هدایت کنید
return response()->json('پروفایل کامل نیست');
}

return $next($request);
}
}
