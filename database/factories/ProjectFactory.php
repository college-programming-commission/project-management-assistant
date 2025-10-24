<?php

namespace Database\Factories;

use Alison\ProjectManagementAssistant\Models\Event;
use Alison\ProjectManagementAssistant\Models\Project;
use Alison\ProjectManagementAssistant\Models\Supervisor;
use Alison\ProjectManagementAssistant\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProjectFactory extends Factory
{
    protected $model = Project::class;
    
    public function definition(): array
    {
        $name = fake()->unique()->sentence(3);

        return [
            'event_id' => Event::factory(),
            'supervisor_id' => Supervisor::factory(),
            'assigned_to' => fake()->optional()->randomElement([User::factory(), null]),
            'slug' => Str::slug($name),
            'name' => $name,
            'appendix' => fake()->optional()->url(),
            'body' => fake()->optional()->paragraphs(3, true),
        ];
    }
}
