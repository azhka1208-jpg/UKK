<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('returns', function (Blueprint $table) {
            $table->id();
            $table->string('code', 40)->unique();
            $table->foreignId('borrowing_id')->unique()->constrained('borrowings')->cascadeOnDelete();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('return_date');
            $table->enum('status', ['diajukan', 'diverifikasi'])->default('diajukan');
            $table->unsignedInteger('late_days')->default(0);
            $table->decimal('fine_amount', 12, 2)->default(0);
            $table->boolean('fine_paid')->default(false);
            $table->text('notes')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('return_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('returns');
    }
};