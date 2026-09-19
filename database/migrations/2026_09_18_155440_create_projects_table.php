<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel `projects` untuk modul manajemen proyek.
     * Rincian anggaran, tenggat waktu, status, dan progres dikelola di sini.
     */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->index()->constrained()->nullOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->string('status')->default('active');
            $table->string('priority')->default('medium');
            $table->string('color')->nullable();
            $table->decimal('budget', 15, 2)->default(0);
            $table->unsignedTinyInteger('progress')->default(0);
            $table->json('tags')->nullable();
            $table->boolean('is_archived')->default(false);
            $table->timestamps();

            $table->index(['status', 'priority']);
            $table->index(['user_id', 'is_archived']);
        });
    }

    /**
     * Hapus tabel `projects` saat rollback.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
