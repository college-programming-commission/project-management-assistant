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
            'name' => $this->faker->randomElement(['Навчальна практика', 'Курсова робота', 'Курсовий проєкт', 'Виробнича практика', 'Переддипломна практика', 'Дипломний проєкт']),
            'freezing_period' => $this->faker->numberBetween(3, 5),
            'course_number' => $this->faker->numberBetween(2, 4),
            'period' => $this->faker->numberBetween(14, 35),
            'attachments' => null,
        ];
    }
}
