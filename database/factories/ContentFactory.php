<?php

namespace Database\Factories;

use App\Models\Content;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContentFactory extends Factory
{

    public function definition(): array
    {
        return [
            'subject' => fake()->sentence(3),
            'content' => fake()->randomHtml(3),
            'content_type' => Content::CONTENT_TYPES[array_rand(Content::CONTENT_TYPES,1)],
        ];
    }

}