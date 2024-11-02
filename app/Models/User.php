<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Http\Controllers\Api\V1\MediaController;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles,MediaUploadingTrait;



    protected $fillable = [
        'name',
        'password',
        'mobile',
        'photo',
        'id',
        'profile_completed',

    ];






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


    public static function updateUserInfo($user,$request)
    {
        if ($request->file('upload_file') ){

            $mediaController = new MediaController();
            $image = $mediaController->moveFileToPermanentStorage($request);
        } else {
            $image = 'nothing to upload';
        }


        $user->update([
            'name'=>$request->input('name'),
            'phone'=>$request->input('phone'),
            'photo'=>$image,
//            'password'=>$request->input('password'),
        ]);
    }

}
