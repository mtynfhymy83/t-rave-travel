<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user')->constrained('users')->onDelete('cascade'); // کلید خارجی به کاربر
            $table->foreignId('parent')->nullable()->constrained('tickets')->onDelete('set null'); // کلید خارجی به تیکت والد
            $table->string('title');
            $table->text('body'); // متن تیکت
            $table->integer('priority'); // اولویت تیکت
            $table->integer('isAnswer')->default(0); // آیا تیکت پاسخ است؟
            $table->timestamps(); // زمان ایجاد و بروزرسانی
            $table->string('reply')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tickets');
    }
};
