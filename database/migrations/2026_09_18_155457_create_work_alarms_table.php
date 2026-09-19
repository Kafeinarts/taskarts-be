<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel `work_alarms` untuk pengingat / alarm waktu kerja.
     */
    public function up(): void
    {
        Schema::create('work_alarms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->index()->constrained()->nullOnDelete();
            $table->string('title');
            $table->time('time');
            $table->json('days')->nullable();
            $table->boolean('enabled')->default(true);
            $table->boolean('snooze')->default(false);
            $table->string('sound')->nullable();
            $table->text('note')->nullable();
            $table->timestamp('last_triggered_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'enabled']);
        });
    }

    /**
     * Hapus tabel `work_alarms` saat rollback.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_alarms');
    }
};
