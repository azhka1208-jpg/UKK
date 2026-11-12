<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('return_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_id')->constrained('returns')->cascadeOnDelete();
            $table->foreignId('tool_id')->constrained('tools')->restrictOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->enum('condition', ['baik', 'rusak_ringan', 'rusak_berat'])->default('baik');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index('return_id');
            $table->index('tool_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('return_details');
    }
};