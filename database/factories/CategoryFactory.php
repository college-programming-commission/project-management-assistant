<?php

namespace Database\Factories;

use Alison\ProjectManagementAssistant\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;
    
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Навчальна практика', 'Курсова робота', 'Курсовий проєкт', 'Виробнича практика', 'Переддипломна практика', 'Дипломний проєкт']),
            'freezing_period' => fake()->numberBetween(3, 5),
            'course_number' => fake()->numberBetween(2, 4),
            'period' => fake()->numberBetween(14, 35),
            'attachments' => null,
        ];
    }
}
