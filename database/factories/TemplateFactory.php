<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TemplateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true), 
            'description' => fake()->sentence(), 
            'body_html' => '<p>' . fake()->paragraph(5) . '</p>', 
            'admin_id' => 1, 

        ];
    }
}
