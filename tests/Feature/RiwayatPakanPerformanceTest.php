<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\FeedExecution;
use App\Models\FeedSchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class RiwayatPakanPerformanceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_prevents_n_plus_1_queries_with_eager_loading()
    {
        // Arrange - Create feed executions with schedules
        $schedules = FeedSchedule::factory()->count(5)->create();
        foreach ($schedules as $schedule) {
            FeedExecution::factory()->count(2)->create([
                'feed_schedule_id' => $schedule->id,
                'trigger_type' => 'scheduled'
            ]);
        }

        // Enable query logging
        DB::enableQueryLog();

        // Act
        $response = $this->actingAs($this->user)->get('/riwayat/pakan');

        // Get query count
        $queries = DB::getQueryLog();
        $queryCount = count($queries);

        // Assert - Should be minimal queries due to eager loading
        // Expected: 1 for auth, 1 for feed_executions with schedule eager load, 6 for statistics
        $this->assertLessThanOrEqual(10, $queryCount,
            "Expected max 10 queries but got {$queryCount}. This may indicate N+1 problem."
        );

        // Verify no queries contain individual schedule fetches in a loop
        $scheduleQueries = array_filter($queries, function ($query) {
            return str_contains($query['query'], 'feed_schedules') &&
                   !str_contains($query['query'], 'in (');
        });

        $this->assertLessThanOrEqual(1, count($scheduleQueries),
            "Found individual schedule queries which indicates N+1 problem"
        );

        DB::disableQueryLog();
    }

    /** @test */
    public function it_performs_efficiently_with_large_dataset()
    {
        // Arrange - Create large dataset
        FeedExecution::factory()->count(100)->create();

        // Act & Measure
        $startTime = microtime(true);
        $response = $this->actingAs($this->user)->get('/riwayat/pakan');
        $executionTime = (microtime(true) - $startTime) * 1000; // Convert to milliseconds

        // Assert
        $response->assertStatus(200);

        // Performance should be under 500ms for 100 records
        $this->assertLessThan(500, $executionTime,
            "Page load took {$executionTime}ms which exceeds 500ms threshold"
        );
    }

    /** @test */
    public function it_efficiently_calculates_statistics_for_large_dataset()
    {
        // Arrange
        FeedExecution::factory()->successful()->count(500)->create();
        FeedExecution::factory()->failed()->count(300)->create();
        FeedExecution::factory()->pending()->count(200)->create();

        // Enable query logging
        DB::enableQueryLog();

        // Act
        $response = $this->actingAs($this->user)->get('/riwayat/pakan');

        // Get statistics-related queries
        $queries = DB::getQueryLog();
        $statisticsQueries = array_filter($queries, function ($query) {
            return str_contains($query['query'], 'count(*)');
        });

        // Assert - Statistics should be calculated with minimal queries (6 count queries)
        $this->assertLessThanOrEqual(6, count($statisticsQueries),
            "Statistics calculation uses too many queries: " . count($statisticsQueries)
        );

        // Verify statistics are correct
        $statistics = $response->viewData('statistics');
        $this->assertEquals(1000, $statistics['total']);
        $this->assertEquals(500, $statistics['success']);
        $this->assertEquals(300, $statistics['failed']);
        $this->assertEquals(200, $statistics['pending']);

        DB::disableQueryLog();
    }

    /** @test */
    public function it_efficiently_filters_large_dataset()
    {
        // Arrange - Create mixed dataset
        FeedExecution::factory()->successful()->count(500)->create();
        FeedExecution::factory()->failed()->count(500)->create();

        // Enable query logging
        DB::enableQueryLog();

        // Act
        $startTime = microtime(true);
        $response = $this->actingAs($this->user)
            ->get('/riwayat/pakan?status=success&per_page=20');
        $executionTime = (microtime(true) - $startTime) * 1000;

        $queries = DB::getQueryLog();

        // Assert
        $response->assertStatus(200);

        // Should use WHERE clause efficiently
        $mainQuery = collect($queries)->first(function ($query) {
            return str_contains($query['query'], 'feed_executions') &&
                   str_contains($query['query'], 'where');
        });

        $this->assertNotNull($mainQuery, "Should use WHERE clause for filtering");
        $this->assertStringContainsString('status', $mainQuery['query']);

        // Performance should still be good with filtering
        $this->assertLessThan(500, $executionTime,
            "Filtered query took {$executionTime}ms which exceeds 500ms threshold"
        );

        DB::disableQueryLog();
    }

    /** @test */
    public function it_uses_pagination_efficiently()
    {
        // Arrange
        FeedExecution::factory()->count(1000)->create();

        // Enable query logging
        DB::enableQueryLog();

        // Act - Request first page
        $response = $this->actingAs($this->user)
            ->get('/riwayat/pakan?per_page=20');

        $queries = DB::getQueryLog();

        // Check for LIMIT clause
        $paginatedQuery = collect($queries)->first(function ($query) {
            return str_contains($query['query'], 'feed_executions') &&
                   str_contains($query['query'], 'limit');
        });

        // Assert
        $this->assertNotNull($paginatedQuery, "Should use LIMIT for pagination");
        $this->assertStringContainsString('limit', strtolower($paginatedQuery['query']));

        // Verify only 20 items loaded
        $feedHistories = $response->viewData('feedHistories');
        $this->assertCount(20, $feedHistories);

        DB::disableQueryLog();
    }

    /** @test */
    public function it_handles_concurrent_filters_efficiently()
    {
        // Arrange
        FeedExecution::factory()->manual()->successful()->count(100)->create();
        FeedExecution::factory()->manual()->failed()->count(50)->create();
        FeedExecution::factory()->scheduled()->successful()->count(150)->create();

        // Enable query logging
        DB::enableQueryLog();

        // Act - Apply multiple filters
        $response = $this->actingAs($this->user)
            ->get('/riwayat/pakan?status=success&trigger_type=manual');

        $queries = DB::getQueryLog();

        // Find the main query
        $mainQuery = collect($queries)->first(function ($query) {
            return str_contains($query['query'], 'feed_executions') &&
                   str_contains($query['query'], 'where');
        });

        // Assert
        $this->assertNotNull($mainQuery);

        // Should combine filters in single WHERE clause
        $this->assertStringContainsString('status', $mainQuery['query']);
        $this->assertStringContainsString('trigger_type', $mainQuery['query']);

        // Verify correct filtering
        $feedHistories = $response->viewData('feedHistories');
        $this->assertCount(20, $feedHistories); // paginated to 20

        $feedHistories->each(function ($execution) {
            $this->assertEquals('manual', $execution->trigger_type);
            $this->assertEquals('success', $execution->status->value);
        });

        DB::disableQueryLog();
    }

    /** @test */
    public function query_execution_time_is_acceptable_with_indexes()
    {
        // Note: This test assumes indexes are created via migration
        // Arrange
        FeedExecution::factory()->count(1000)->create();

        // Act - Measure query time
        DB::enableQueryLog();
        $startTime = microtime(true);

        $this->actingAs($this->user)
            ->get('/riwayat/pakan?status=success&trigger_type=scheduled');

        $queryTime = (microtime(true) - $startTime) * 1000;
        $queries = DB::getQueryLog();

        // Assert
        // With proper indexing, query should be fast even with 1000 records
        $this->assertLessThan(200, $queryTime,
            "Query execution took {$queryTime}ms. Check if indexes are properly created."
        );

        // Check that query uses indexes (this is database-specific)
        // For MySQL/MariaDB, we expect the query to use indexes on status, trigger_type, and updated_at
        $mainQuery = collect($queries)->first(function ($query) {
            return str_contains($query['query'], 'feed_executions') &&
                   str_contains($query['query'], 'order by');
        });

        $this->assertNotNull($mainQuery);
        $this->assertStringContainsString('order by', strtolower($mainQuery['query']));
        $this->assertStringContainsString('updated_at', strtolower($mainQuery['query']));

        DB::disableQueryLog();
    }

    /** @test */
    public function it_efficiently_loads_only_required_columns_for_schedule()
    {
        // Arrange
        $schedule = FeedSchedule::factory()->create(['name' => 'Test Schedule']);
        FeedExecution::factory()->count(5)->create([
            'feed_schedule_id' => $schedule->id,
            'trigger_type' => 'scheduled'
        ]);

        // Enable query logging
        DB::enableQueryLog();

        // Act
        $response = $this->actingAs($this->user)->get('/riwayat/pakan');

        // Get schedule-related query
        $queries = DB::getQueryLog();
        $scheduleQuery = collect($queries)->first(function ($query) {
            return str_contains($query['query'], 'feed_schedules');
        });

        // Assert - Should select only id and name columns
        $this->assertNotNull($scheduleQuery);

        // Check if using select with specific columns
        $queryStr = strtolower($scheduleQuery['query']);

        // Should contain select with limited columns, not SELECT *
        if (str_contains($queryStr, 'select *')) {
            $this->fail("Query is selecting all columns from feed_schedules. Should only select 'id' and 'name'");
        }

        DB::disableQueryLog();
    }
}
