<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel `tasks` untuk modul daftar tugas / to-do list.
     * Setiap tugas dapat dikaitkan ke proyek (opsional).
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->index()->constrained()->nullOnDelete();
            $table->foreignId('project_id')->nullable()->index()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('due_date')->nullable();
            $table->string('status')->default('todo');
            $table->string('priority')->default('medium');
            $table->string('category')->nullable();
            $table->json('tags')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->unsignedInteger('time_spent_minutes')->default(0);
            $table->timestamps();

            $table->index(['status', 'due_date']);
            $table->index(['user_id', 'is_completed']);
            $table->index(['project_id', 'status']);
        });
    }

    /**
     * Hapus tabel `tasks` saat rollback.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
