<?php

namespace Database\Factories\Movies;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Movies\Movie>
 */
class MovieFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
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
