<?php

namespace Database\Factories;

use Alison\ProjectManagementAssistant\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'remember_token' => Str::random(10),
            'profile_photo_path' => fake()->optional()->randomElement([
                'https://placehold.co/400x400/94A3B8/FFFFFF?text=User',
                'https://placehold.co/400x400/64748B/FFFFFF?text=Avatar',
            ]),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'middle_name' => fake()->boolean(70) ? fake()->firstName() : null,
            'description' => fake()->boolean(80) ? fake()->text(200) : null,
            'avatar' => fake()->optional()->randomElement([
                'https://placehold.co/400x400/94A3B8/FFFFFF?text=User',
                'https://placehold.co/400x400/64748B/FFFFFF?text=Avatar',
            ]),
            'course_number' => fake()->boolean(80) ? fake()->numberBetween(2, 4) : null,
        ];
    }

    public function unverified(): static
    {
        return $this->state(['email_verified_at' => null]);
    }

    public function withPersonalTeam(): static
    {
        return $this;
    }
}
