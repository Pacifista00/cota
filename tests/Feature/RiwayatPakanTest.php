<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\FeedExecution;
use App\Models\FeedSchedule;
use App\Enums\FeedExecutionStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RiwayatPakanTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_can_display_riwayat_pakan_page()
    {
        // Act
        $response = $this->actingAs($this->user)->get('/riwayat/pakan');

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('riwayat-pakan');
        $response->assertViewHas(['active', 'feedHistories', 'statistics', 'filters']);
    }

    /** @test */
    public function it_displays_feed_executions_ordered_by_updated_at_desc()
    {
        // Arrange
        $oldExecution = FeedExecution::factory()->create([
            'updated_at' => now()->subHours(2)
        ]);
        $newExecution = FeedExecution::factory()->create([
            'updated_at' => now()
        ]);

        // Act
        $response = $this->actingAs($this->user)->get('/riwayat/pakan');

        // Assert
        $feedHistories = $response->viewData('feedHistories');
        $this->assertEquals($newExecution->id, $feedHistories->first()->id);
        $this->assertEquals($oldExecution->id, $feedHistories->last()->id);
    }

    /** @test */
    public function it_can_filter_by_status()
    {
        // Arrange
        FeedExecution::factory()->create(['status' => FeedExecutionStatus::SUCCESS]);
        FeedExecution::factory()->create(['status' => FeedExecutionStatus::FAILED]);
        FeedExecution::factory()->create(['status' => FeedExecutionStatus::PENDING]);

        // Act
        $response = $this->actingAs($this->user)->get('/riwayat/pakan?status=success');

        // Assert
        $feedHistories = $response->viewData('feedHistories');
        $this->assertCount(1, $feedHistories);
        $this->assertEquals(FeedExecutionStatus::SUCCESS, $feedHistories->first()->status);
    }

    /** @test */
    public function it_can_filter_by_trigger_type()
    {
        // Arrange
        FeedExecution::factory()->manual()->create();
        FeedExecution::factory()->manual()->create();
        FeedExecution::factory()->scheduled()->create();

        // Act
        $response = $this->actingAs($this->user)->get('/riwayat/pakan?trigger_type=manual');

        // Assert
        $feedHistories = $response->viewData('feedHistories');
        $this->assertCount(2, $feedHistories);
        $feedHistories->each(function ($execution) {
            $this->assertEquals('manual', $execution->trigger_type);
        });
    }

    /** @test */
    public function it_can_filter_by_both_status_and_trigger_type()
    {
        // Arrange
        FeedExecution::factory()->manual()->successful()->create();
        FeedExecution::factory()->manual()->failed()->create();
        FeedExecution::factory()->scheduled()->successful()->create();

        // Act
        $response = $this->actingAs($this->user)
            ->get('/riwayat/pakan?status=success&trigger_type=manual');

        // Assert
        $feedHistories = $response->viewData('feedHistories');
        $this->assertCount(1, $feedHistories);
        $this->assertEquals('manual', $feedHistories->first()->trigger_type);
        $this->assertEquals(FeedExecutionStatus::SUCCESS, $feedHistories->first()->status);
    }

    /** @test */
    public function it_can_change_items_per_page()
    {
        // Arrange
        FeedExecution::factory()->count(15)->create();

        // Act - Default (20 per page)
        $response1 = $this->actingAs($this->user)->get('/riwayat/pakan');
        $feedHistories1 = $response1->viewData('feedHistories');
        $this->assertEquals(15, $feedHistories1->count());

        // Act - 10 per page
        $response2 = $this->actingAs($this->user)->get('/riwayat/pakan?per_page=10');
        $feedHistories2 = $response2->viewData('feedHistories');
        $this->assertEquals(10, $feedHistories2->count());
    }

    /** @test */
    public function it_calculates_statistics_correctly()
    {
        // Arrange
        FeedExecution::factory()->successful()->count(5)->create();
        FeedExecution::factory()->failed()->count(3)->create();
        FeedExecution::factory()->pending()->count(2)->create();
        FeedExecution::factory()->manual()->count(4)->create();
        FeedExecution::factory()->scheduled()->count(6)->create();

        // Act
        $response = $this->actingAs($this->user)->get('/riwayat/pakan');

        // Assert
        $statistics = $response->viewData('statistics');
        $this->assertEquals(10, $statistics['total']);
        $this->assertEquals(5, $statistics['success']);
        $this->assertEquals(3, $statistics['failed']);
        $this->assertEquals(2, $statistics['pending']);
        $this->assertEquals(4, $statistics['manual']);
        $this->assertEquals(6, $statistics['scheduled']);
    }

    /** @test */
    public function it_eager_loads_schedule_relationship()
    {
        // Arrange
        $schedule = FeedSchedule::factory()->create(['name' => 'Test Schedule']);
        FeedExecution::factory()->create([
            'feed_schedule_id' => $schedule->id,
            'trigger_type' => 'scheduled'
        ]);

        // Act
        $response = $this->actingAs($this->user)->get('/riwayat/pakan');

        // Assert
        $feedHistories = $response->viewData('feedHistories');
        $this->assertTrue($feedHistories->first()->relationLoaded('schedule'));
        $this->assertEquals('Test Schedule', $feedHistories->first()->schedule->name);
    }

    /** @test */
    public function it_preserves_filter_parameters_in_pagination()
    {
        // Arrange
        FeedExecution::factory()->successful()->count(25)->create();

        // Act
        $response = $this->actingAs($this->user)
            ->get('/riwayat/pakan?status=success&per_page=10');

        // Assert
        $feedHistories = $response->viewData('feedHistories');
        $paginationUrl = $feedHistories->url(2);

        $this->assertStringContainsString('status=success', $paginationUrl);
        $this->assertStringContainsString('per_page=10', $paginationUrl);
    }

    /** @test */
    public function it_shows_all_items_when_filter_is_set_to_all()
    {
        // Arrange
        FeedExecution::factory()->successful()->create();
        FeedExecution::factory()->failed()->create();
        FeedExecution::factory()->pending()->create();

        // Act
        $response = $this->actingAs($this->user)->get('/riwayat/pakan?status=all');

        // Assert
        $feedHistories = $response->viewData('feedHistories');
        $this->assertCount(3, $feedHistories);
    }

    /** @test */
    public function it_requires_authentication_to_access_riwayat_pakan()
    {
        // Act
        $response = $this->get('/riwayat/pakan');

        // Assert
        $response->assertRedirect('/login');
    }

    /** @test */
    public function it_displays_empty_state_when_no_executions_exist()
    {
        // Act
        $response = $this->actingAs($this->user)->get('/riwayat/pakan');

        // Assert
        $response->assertStatus(200);
        $feedHistories = $response->viewData('feedHistories');
        $this->assertCount(0, $feedHistories);

        $statistics = $response->viewData('statistics');
        $this->assertEquals(0, $statistics['total']);
    }
}
