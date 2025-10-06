<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\FeedExecution;
use App\Models\FeedSchedule;
use App\Models\User;
use App\Enums\FeedExecutionStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FeedExecutionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function it_can_filter_successful_executions()
    {
        // Arrange
        FeedExecution::factory()->create(['status' => FeedExecutionStatus::SUCCESS]);
        FeedExecution::factory()->create(['status' => FeedExecutionStatus::FAILED]);
        FeedExecution::factory()->create(['status' => FeedExecutionStatus::PENDING]);

        // Act
        $successfulExecutions = FeedExecution::successful()->get();

        // Assert
        $this->assertCount(1, $successfulExecutions);
        $this->assertEquals(FeedExecutionStatus::SUCCESS, $successfulExecutions->first()->status);
    }

    /** @test */
    public function it_can_filter_failed_executions()
    {
        // Arrange
        FeedExecution::factory()->create(['status' => FeedExecutionStatus::SUCCESS]);
        FeedExecution::factory()->create(['status' => FeedExecutionStatus::FAILED]);
        FeedExecution::factory()->create(['status' => FeedExecutionStatus::FAILED]);

        // Act
        $failedExecutions = FeedExecution::failed()->get();

        // Assert
        $this->assertCount(2, $failedExecutions);
    }

    /** @test */
    public function it_can_filter_pending_executions()
    {
        // Arrange
        FeedExecution::factory()->create(['status' => FeedExecutionStatus::SUCCESS]);
        FeedExecution::factory()->create(['status' => FeedExecutionStatus::PENDING]);

        // Act
        $pendingExecutions = FeedExecution::pending()->get();

        // Assert
        $this->assertCount(1, $pendingExecutions);
        $this->assertEquals(FeedExecutionStatus::PENDING, $pendingExecutions->first()->status);
    }

    /** @test */
    public function it_can_filter_manual_executions()
    {
        // Arrange
        FeedExecution::factory()->create(['trigger_type' => 'manual']);
        FeedExecution::factory()->create(['trigger_type' => 'scheduled']);
        FeedExecution::factory()->create(['trigger_type' => 'manual']);

        // Act
        $manualExecutions = FeedExecution::manual()->get();

        // Assert
        $this->assertCount(2, $manualExecutions);
    }

    /** @test */
    public function it_can_filter_scheduled_executions()
    {
        // Arrange
        FeedExecution::factory()->create(['trigger_type' => 'manual']);
        FeedExecution::factory()->create(['trigger_type' => 'scheduled']);

        // Act
        $scheduledExecutions = FeedExecution::scheduled()->get();

        // Assert
        $this->assertCount(1, $scheduledExecutions);
        $this->assertEquals('scheduled', $scheduledExecutions->first()->trigger_type);
    }

    /** @test */
    public function it_can_check_if_execution_is_successful()
    {
        // Arrange
        $execution = FeedExecution::factory()->create([
            'status' => FeedExecutionStatus::SUCCESS
        ]);

        // Assert
        $this->assertTrue($execution->isSuccessful());
        $this->assertFalse($execution->isFailed());
        $this->assertFalse($execution->isPending());
    }

    /** @test */
    public function it_can_check_if_execution_is_failed()
    {
        // Arrange
        $execution = FeedExecution::factory()->create([
            'status' => FeedExecutionStatus::FAILED
        ]);

        // Assert
        $this->assertTrue($execution->isFailed());
        $this->assertFalse($execution->isSuccessful());
        $this->assertFalse($execution->isPending());
    }

    /** @test */
    public function it_can_check_if_execution_is_pending()
    {
        // Arrange
        $execution = FeedExecution::factory()->create([
            'status' => FeedExecutionStatus::PENDING
        ]);

        // Assert
        $this->assertTrue($execution->isPending());
        $this->assertFalse($execution->isSuccessful());
        $this->assertFalse($execution->isFailed());
    }

    /** @test */
    public function it_belongs_to_a_schedule()
    {
        // Arrange
        $user = User::factory()->create();
        $schedule = FeedSchedule::factory()->create(['user_id' => $user->id]);
        $execution = FeedExecution::factory()->create([
            'feed_schedule_id' => $schedule->id
        ]);

        // Act & Assert
        $this->assertInstanceOf(FeedSchedule::class, $execution->schedule);
        $this->assertEquals($schedule->id, $execution->schedule->id);
    }

    /** @test */
    public function it_can_have_null_schedule_for_manual_execution()
    {
        // Arrange
        $execution = FeedExecution::factory()->create([
            'feed_schedule_id' => null,
            'trigger_type' => 'manual'
        ]);

        // Act & Assert
        $this->assertNull($execution->schedule);
        $this->assertEquals('manual', $execution->trigger_type);
    }
}
