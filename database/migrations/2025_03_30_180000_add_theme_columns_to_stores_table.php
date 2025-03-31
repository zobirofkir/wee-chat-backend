<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            if (!Schema::hasColumn('stores', 'theme')) {
                $table->string('theme')->nullable();
            }

            if (!Schema::hasColumn('stores', 'theme_applied_at')) {
                $table->timestamp('theme_applied_at')->nullable();
            }

            if (!Schema::hasColumn('stores', 'theme_storage_path')) {
                $table->string('theme_storage_path')->nullable();
            }

            if (!Schema::hasColumn('stores', 'theme_data')) {
                $table->json('theme_data')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn([
                'theme',
                'theme_applied_at',
                'theme_storage_path',
                'theme_data'
            ]);
        });
    }
};
