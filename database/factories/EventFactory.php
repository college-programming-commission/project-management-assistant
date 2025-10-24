<?php

namespace Database\Factories;

use Alison\ProjectManagementAssistant\Models\Category;
use Alison\ProjectManagementAssistant\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-1 week', '+1 month');

        return [
            'category_id' => Category::factory(),
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->optional()->text(200),
            'start_date' => $startDate,
            'end_date' => $this->faker->dateTimeBetween($startDate, '+2 months'),
            'bg_color' => $this->faker->optional()->hexColor(),
            'fg_color' => $this->faker->optional()->hexColor(),
            'image' => $this->faker->optional()->randomElement([
                'https://placehold.co/640x480/6366F1/FFFFFF?text=Event',
                'https://placehold.co/640x480/EC4899/FFFFFF?text=Activity',
                'https://placehold.co/640x480/14B8A6/FFFFFF?text=Session',
            ]),
        ];
    }
}
