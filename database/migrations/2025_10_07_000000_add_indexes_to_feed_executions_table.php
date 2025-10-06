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
            // Index untuk sorting performance (ORDER BY updated_at DESC)
            $table->index('updated_at', 'idx_updated_at');

            // Composite index untuk filtering (WHERE status = X AND trigger_type = Y)
            $table->index(['status', 'trigger_type'], 'idx_status_type');

            // Index untuk foreign key optimization (JOIN dengan feed_schedules)
            $table->index('feed_schedule_id', 'idx_feed_schedule_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feed_executions', function (Blueprint $table) {
            // Drop indexes in reverse order
            $table->dropIndex('idx_feed_schedule_id');
            $table->dropIndex('idx_status_type');
            $table->dropIndex('idx_updated_at');
        });
    }
};
