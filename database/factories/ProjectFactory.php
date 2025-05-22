<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class ProjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nama_project' => $this->faker->sentence(3),
            'deskripsi' => $this->faker->paragraph,
            'tanggal_mulai' => $start = Carbon::instance($this->faker->dateTimeThisDecade())->format('Y-m-d'),
            'tanggal_selesai' => Carbon::parse($start)->addDays(30)->format('Y-m-d'),
        ];
    }
}
