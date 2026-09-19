<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel `events` untuk kalender / agenda (event planner).
     */
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->index()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('start_at');
            $table->dateTime('end_at')->nullable();
            $table->boolean('all_day')->default(false);
            $table->string('calendar')->nullable()->default('personal');
            $table->string('color')->nullable();
            $table->string('location')->nullable();
            $table->timestamps();

            $table->index(['start_at', 'end_at']);
            $table->index(['user_id', 'calendar']);
        });
    }

    /**
     * Hapus tabel `events` saat rollback.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
