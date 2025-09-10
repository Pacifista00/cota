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
        Schema::create('feed_executions', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('feed_schedule_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['success', 'failed']);
            $table->timestamp('executed_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feed_executions');
    }
};
