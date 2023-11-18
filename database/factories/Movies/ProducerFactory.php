<?php

namespace Database\Factories\Movies;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProducerFactory extends Factory
{

    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
        ];
    }
}
