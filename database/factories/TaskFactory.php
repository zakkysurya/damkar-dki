<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ManPower;
use App\Models\Project;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'judul_task' => $this->faker->sentence(4),
            'man_power_id' => ManPower::inRandomOrder()->first()?->id ?? ManPower::factory(),
            'status' => $this->faker->randomElement(['in_progress', 'pending', 'done']),
            'project_id' => Project::inRandomOrder()->first()?->id ?? Project::factory(),
        ];
    }
}
