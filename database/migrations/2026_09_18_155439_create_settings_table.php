<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel `settings` untuk menyimpan pengaturan aplikasi secara key-value.
     * Dipakai sebagai penyimpanan modular untuk user profile, business profile, dan preferensi.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->index();
            $table->text('value')->nullable();
            $table->string('type')->default('string');
            $table->string('group')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Hapus tabel `settings` saat rollback.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
