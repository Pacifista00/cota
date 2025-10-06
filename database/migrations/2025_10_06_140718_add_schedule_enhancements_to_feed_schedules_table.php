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
        Schema::table('feed_schedules', function (Blueprint $table) {
            // Add user relationship
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            
            // Add schedule date range
            $table->date('start_date')->nullable()->after('waktu_pakan');
            $table->date('end_date')->nullable()->after('start_date');
            
            // Add schedule control
            $table->boolean('is_active')->default(true)->after('end_date');
            
            // Add frequency support for future extensibility
            $table->string('frequency_type')->default('daily')->after('is_active');
            $table->json('frequency_data')->nullable()->after('frequency_type');
            
            // Add name and description for better UX
            $table->string('name')->nullable()->after('user_id');
            $table->text('description')->nullable()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feed_schedules', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn([
                'user_id',
                'start_date',
                'end_date',
                'is_active',
                'frequency_type',
                'frequency_data',
                'name',
                'description'
            ]);
        });
    }
};
