<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProfileCompletedToUsersTable extends Migration
{
public function up()
{
Schema::table('users', function (Blueprint $table) {
$table->boolean('profile_completed')->default(false); // افزودن فیلد به عنوان boolean
});
}

public function down()
{
Schema::table('users', function (Blueprint $table) {
$table->dropColumn('profile_completed'); // حذف فیلد در صورت برگشت
});
}
}
