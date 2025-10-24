<?php

namespace Database\Seeders;

use Alison\ProjectManagementAssistant\Models\Category;
use Alison\ProjectManagementAssistant\Models\Subject;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Навчальна практика №1 (2 курс)', 'freezing_period' => 3, 'course_number' => 2, 'period' => 14],
            ['name' => 'Навчальна практика №2 (2 курс)', 'freezing_period' => 3, 'course_number' => 2, 'period' => 21],
            ['name' => 'Курсова робота', 'freezing_period' => 5, 'course_number' => 2, 'period' => 14],
            ['name' => 'Навчальна практика №1 (3 курс)', 'freezing_period' => 3, 'course_number' => 3, 'period' => 14],
            ['name' => 'Навчальна практика №2 (3 курс)', 'freezing_period' => 3, 'course_number' => 3, 'period' => 21],
            ['name' => 'Курсовий проєкт', 'freezing_period' => 5, 'course_number' => 3, 'period' => 21],
            ['name' => 'Виробнича практика', 'freezing_period' => 3, 'course_number' => 4, 'period' => 35],
            ['name' => 'Переддипломна практика', 'freezing_period' => 5, 'course_number' => 4, 'period' => 35],
            ['name' => 'Дипломний проєкт', 'freezing_period' => 5, 'course_number' => 4, 'period' => 35],
        ];

        foreach ($categories as $categoryData) {
            $category = Category::query()->firstOrCreate(
                ['name' => $categoryData['name']],
                [
                    'freezing_period' => $categoryData['freezing_period'],
                    'course_number' => $categoryData['course_number'],
                    'period' => $categoryData['period'],
                ]
            );

            if ($category->subjects()->count() === 0) {
                $subjects = Subject::query()
                    ->where('course_number', $category->course_number)
                    ->inRandomOrder()
                    ->limit(rand(1, 3))
                    ->get();

                if ($subjects->isNotEmpty()) {
                    $category->subjects()->attach($subjects);
                }
            }
        }
    }
}
