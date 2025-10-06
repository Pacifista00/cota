<?php

namespace Database\Factories;

use App\Models\FeedSchedule;
use App\Models\User;
use App\Enums\ScheduleFrequency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FeedSchedule>
 */
class FeedScheduleFactory extends Factory
{
    protected $model = FeedSchedule::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->words(2, true),
            'description' => $this->faker->sentence(),
            'waktu_pakan' => $this->faker->time('H:i:s'),
            'start_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'end_date' => $this->faker->optional()->dateTimeBetween('now', '+1 month'),
            'is_active' => $this->faker->boolean(80), // 80% active
            'frequency_type' => ScheduleFrequency::DAILY,
            'frequency_data' => null,
            'last_executed_at' => $this->faker->optional()->dateTimeBetween('-1 week', 'now'),
        ];
    }

    /**
     * Indicate that the schedule is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the schedule is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
