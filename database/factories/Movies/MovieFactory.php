<?php

namespace Database\Factories\Movies;

use Illuminate\Database\Eloquent\Factories\Factory;

class MovieFactory extends Factory
{

    public function definition(): array
    {
        return [
            'year' => $this->faker->numberBetween(1900, 2020),
            'title' => $this->faker->sentence(3),
            'studios' => $this->faker->company,
            'producers' => $this->faker->name,
            'winner' => $this->faker->boolean(50),
        ];
    }
}
