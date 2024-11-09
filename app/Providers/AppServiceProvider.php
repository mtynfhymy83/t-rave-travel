<?php
namespace App\Providers;

use App\services\AuthService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
public function register()
{

$this->app->singleton(AuthService::class, function ($app) {
return new AuthService();
});
}

public function boot()
{
//
}
}
