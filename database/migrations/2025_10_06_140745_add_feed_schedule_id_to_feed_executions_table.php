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
        Schema::table('feed_executions', function (Blueprint $table) {
            // Add relationship to feed_schedules
            $table->foreignId('feed_schedule_id')->nullable()->after('id')->constrained()->onDelete('set null');
            
            // Add trigger_type to distinguish manual vs scheduled feeds
            $table->enum('trigger_type', ['manual', 'scheduled', 'api'])->default('manual')->after('feed_schedule_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feed_executions', function (Blueprint $table) {
            $table->dropForeign(['feed_schedule_id']);
            $table->dropColumn(['feed_schedule_id', 'trigger_type']);
        });
    }
};
