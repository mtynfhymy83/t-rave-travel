<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepartmentSubsTable extends Migration
{
public function up()
{
Schema::create('department_subs', function (Blueprint $table) {
$table->id(); // شناسه یکتا
$table->string('title'); // عنوان زیر دپارتمان
$table->foreignId('parent')->constrained('users')->onDelete('cascade'); // کلید خارجی به کاربر
$table->timestamps(); // زمان ایجاد و بروزرسانی
});
}

public function down()
{
Schema::dropIfExists('department_subs');
}
}
