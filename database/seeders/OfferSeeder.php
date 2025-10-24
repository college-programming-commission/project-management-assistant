<?php

namespace Database\Seeders;

use Alison\ProjectManagementAssistant\Models\Offer;
use Alison\ProjectManagementAssistant\Models\Project;
use Alison\ProjectManagementAssistant\Models\User;
use Illuminate\Database\Seeder;

class OfferSeeder extends Seeder
{
    public function run(): void
    {
        if (Project::query()->count() === 0) {
            $this->call(ProjectSeeder::class);
        }

        if (User::query()->count() === 0) {
            $this->call(UserSeeder::class);
        }

        $projects = Project::all()->where('assigned_to', null);
        $users = User::role('student')->get();

        if ($projects->isEmpty() || $users->isEmpty()) {
            return;
        }

        $created = 0;
        $maxAttempts = 100;
        $attempts = 0;

        while ($created < 30 && $attempts < $maxAttempts) {
            $projectId = $projects->random()->id;
            $studentId = $users->random()->id;

            $existing = Offer::query()
                ->where('project_id', $projectId)
                ->where('student_id', $studentId)
                ->exists();

            if (!$existing) {
                Offer::query()->create([
                    'project_id' => $projectId,
                    'student_id' => $studentId,
                ]);
                $created++;
            }

            $attempts++;
        }
    }
}
