<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RecipientFactory extends Factory
{

    public function definition(): array
    {
        return [
            'email' => fake()->email(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName()
        ];
    }

    public function invalid(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName()
        ];
    }
}