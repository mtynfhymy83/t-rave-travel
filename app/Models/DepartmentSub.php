<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepartmentSub extends Model
{
use HasFactory;

protected $fillable = [
'title',
'parent',
];

public function parentUser()
{
return $this->belongsTo(User::class, 'parent');
}
}
