<?php

namespace Database\Factories;

use Alison\ProjectManagementAssistant\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SubjectFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->words(3, true);

        return [
            'slug' => Str::slug($name),
            'name' => ucfirst($name),
            'course_number' => $this->faker->numberBetween(2, 4),
            'description' => $this->faker->optional()->paragraph(),
            'image' => $this->faker->optional()->randomElement([
                'https://placehold.co/640x480/3B82F6/FFFFFF?text=Subject',
                'https://placehold.co/640x480/10B981/FFFFFF?text=Course',
                'https://placehold.co/640x480/8B5CF6/FFFFFF?text=Education',
            ]),
        ];
    }
}
