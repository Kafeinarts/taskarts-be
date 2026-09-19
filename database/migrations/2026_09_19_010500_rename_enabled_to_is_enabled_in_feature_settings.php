<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feature_settings', function (Blueprint $table) {
            $table->dropColumn('enabled');
        });
        Schema::table('feature_settings', function (Blueprint $table) {
            $table->tinyInteger('is_enabled')->default(1)->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('feature_settings', function (Blueprint $table) {
            $table->dropColumn('is_enabled');
        });
        Schema::table('feature_settings', function (Blueprint $table) {
            $table->boolean('enabled')->default(true)->after('description');
        });
    }
};
