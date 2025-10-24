<?php

namespace Database\Factories;

use Alison\ProjectManagementAssistant\Models\Event;
use Alison\ProjectManagementAssistant\Models\Subevent;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubeventFactory extends Factory
{
    protected $model = Subevent::class;
    
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-1 week', '+1 month');

        return [
            'event_id' => Event::factory(),
            'depends_on' => null,
            'name' => fake()->sentence(3),
            'description' => fake()->optional()->text(200),
            'start_date' => $startDate,
            'end_date' => fake()->dateTimeBetween($startDate, '+2 months'),
            'bg_color' => fake()->optional()->hexColor(),
            'fg_color' => fake()->optional()->hexColor(),
        ];
    }
}
