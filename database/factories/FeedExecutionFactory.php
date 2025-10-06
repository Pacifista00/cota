<?php

namespace Database\Factories;

use App\Models\FeedExecution;
use App\Models\FeedSchedule;
use App\Enums\FeedExecutionStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FeedExecution>
 */
class FeedExecutionFactory extends Factory
{
    protected $model = FeedExecution::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'feed_schedule_id' => null,
            'trigger_type' => $this->faker->randomElement(['manual', 'scheduled']),
            'status' => $this->faker->randomElement([
                FeedExecutionStatus::SUCCESS,
                FeedExecutionStatus::FAILED,
                FeedExecutionStatus::PENDING,
            ]),
            'executed_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }

    /**
     * Indicate that the execution is manual.
     */
    public function manual(): static
    {
        return $this->state(fn (array $attributes) => [
            'trigger_type' => 'manual',
            'feed_schedule_id' => null,
        ]);
    }

    /**
     * Indicate that the execution is scheduled.
     */
    public function scheduled(): static
    {
        return $this->state(fn (array $attributes) => [
            'trigger_type' => 'scheduled',
            'feed_schedule_id' => FeedSchedule::factory(),
        ]);
    }

    /**
     * Indicate that the execution is successful.
     */
    public function successful(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => FeedExecutionStatus::SUCCESS,
        ]);
    }

    /**
     * Indicate that the execution is failed.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => FeedExecutionStatus::FAILED,
        ]);
    }

    /**
     * Indicate that the execution is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => FeedExecutionStatus::PENDING,
        ]);
    }
}
