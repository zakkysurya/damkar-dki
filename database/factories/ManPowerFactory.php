<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ManPowerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nama_lengkap' => $this->faker->name,
            'jabatan' => $this->faker->jobTitle,
            'no_telepon' => $this->faker->phoneNumber,
        ];
    }
}
