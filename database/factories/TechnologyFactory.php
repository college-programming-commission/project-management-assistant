<?php

namespace Database\Factories;

use Alison\ProjectManagementAssistant\Models\Technology;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TechnologyFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->word();

        return [
            'slug' => Str::slug($name),
            'name' => ucfirst($name),
            'description' => $this->faker->optional()->paragraph(),
            'image' => $this->faker->optional()->randomElement([
                'https://placehold.co/640x480/F59E0B/FFFFFF?text=Tech',
                'https://placehold.co/640x480/EF4444/FFFFFF?text=Technology',
                'https://placehold.co/640x480/06B6D4/FFFFFF?text=Stack',
            ]),
            'link' => $this->faker->optional()->url(),
        ];
    }
}
