<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('borrowings', function (Blueprint $table) {
            $table->id();
            $table->string('code', 40)->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', [
                'menunggu',
                'disetujui',
                'ditolak',
                'dipinjam',
                'selesai',
                'terlambat',
            ])->default('menunggu');
            $table->date('borrow_date');
            $table->date('due_date');
            $table->text('purpose');
            $table->text('notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('picked_up_at')->nullable();
            $table->unsignedInteger('total_items')->default(0);
            $table->timestamps();

            $table->index('user_id');
            $table->index('status');
            $table->index('borrow_date');
            $table->index('due_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('borrowings');
    }
};