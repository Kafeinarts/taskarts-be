<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel `habits` untuk modul pelacakan kebiasaan (habit tracker).
     */
    public function up(): void
    {
        Schema::create('habits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->index()->constrained()->nullOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('frequency')->default('daily');
            $table->string('category')->nullable();
            $table->string('color')->nullable();
            $table->string('icon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('streak')->default(0);
            $table->decimal('target', 12, 2)->unsigned()->nullable();
            $table->string('goal_type')->default('habit');
            $table->timestamps();

            $table->index(['user_id', 'is_active']);
            $table->index(['frequency', 'category']);
        });
    }

    /**
     * Hapus tabel `habits` saat rollback.
     */
    public function down(): void
    {
        Schema::dropIfExists('habits');
    }
};
