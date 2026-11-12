<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tools', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete()->cascadeOnUpdate();
            $table->text('description')->nullable();
            $table->unsignedInteger('stock_total')->default(0);
            $table->unsignedInteger('stock_available')->default(0);
            $table->enum('condition', ['baik', 'rusak_ringan', 'rusak_berat'])->default('baik');
            $table->string('location')->nullable();
            $table->string('photo')->nullable();
            $table->enum('status', ['tersedia', 'perbaikan', 'tidak_tersedia'])->default('tersedia');
            $table->timestamps();

            $table->index('category_id');
            $table->index('condition');
            $table->index('status');
            $table->index('name');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tools');
    }
};