<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepartmentsTable extends Migration
{
public function up()
{
Schema::create('departments', function (Blueprint $table) {
$table->id(); // شناسه یکتا
$table->string('title')->unique(); // عنوان دپارتمان
$table->timestamps(); // زمان ایجاد و بروزرسانی
});
}

public function down()
{
Schema::dropIfExists('departments');
}
}
