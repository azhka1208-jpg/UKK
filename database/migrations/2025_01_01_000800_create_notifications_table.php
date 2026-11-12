<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('message');
            $table->string('type', 30)->default('info');
            $table->boolean('is_read')->default(false);
            $table->string('url')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('is_read');
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};