<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\ManPower;
use App\Models\Task;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Project::factory(20)->create();
        ManPower::factory(20)->create();
        Task::factory(20)->create();
    }
}
