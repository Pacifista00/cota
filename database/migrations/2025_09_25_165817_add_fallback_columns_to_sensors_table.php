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
        Schema::table('sensors', function (Blueprint $table) {
            $table->enum('data_source', ['REAL_TIME', 'FALLBACK', 'BACKFILLED'])->default('REAL_TIME');
            $table->boolean('is_estimated')->default(false);
            $table->timestamp('reading_timestamp')->nullable()->comment('Actual sensor reading time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sensors', function (Blueprint $table) {
            $table->dropColumn(['data_source', 'is_estimated', 'reading_timestamp']);
        });
    }
};
