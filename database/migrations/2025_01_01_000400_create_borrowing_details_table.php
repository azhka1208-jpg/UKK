<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('borrowing_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('borrowing_id')->constrained('borrowings')->cascadeOnDelete();
            $table->foreignId('tool_id')->constrained('tools')->restrictOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->string('note')->nullable();
            $table->timestamps();

            $table->index('borrowing_id');
            $table->index('tool_id');
            $table->unique(['borrowing_id', 'tool_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('borrowing_details');
    }
};