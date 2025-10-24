<?php

namespace Database\Seeders;

use Alison\ProjectManagementAssistant\Models\Subject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            ['name' => 'Основи програмування та алгоритмічні мови', 'course_number' => 2],
            ['name' => 'Операційні системи', 'course_number' => 3],
            ['name' => 'Офісне програмне забезпечення', 'course_number' => 2],
            ['name' => 'Вища математика', 'course_number' => 2],
            ['name' => 'Фізика', 'course_number' => 2],
            ['name' => 'Дискретна математика', 'course_number' => 3],
            ['name' => 'Основи електротехніки та електроніки', 'course_number' => 1],
            ['name' => 'Основи програмної інженерії та проєктний практикум', 'course_number' => 3],
            ['name' => 'Об\'єктоорієнтоване програмування', 'course_number' => 3],
            ['name' => 'Алгоритми та структури даних', 'course_number' => 3],
            ['name' => 'Бази даних', 'course_number' => 3],
            ['name' => 'Людино-машинний інтерфейс', 'course_number' => 3],
            ['name' => 'Комп\'ютерна схемотехніка та архітектура комп\'ютера', 'course_number' => 3],
            ['name' => 'Web-дизайн', 'course_number' => 3],
            ['name' => 'Конструювання програмного забезпечення', 'course_number' => 4],
            ['name' => 'Чисельні методи та програмування', 'course_number' => 4],
            ['name' => 'Організація комп\'ютерних мереж', 'course_number' => 4],
            ['name' => 'Інструментальні засоби візуального програмування', 'course_number' => 4],
            ['name' => 'Основи хмарних технологій', 'course_number' => 4],
            ['name' => 'Кібербезпека та соціальна інженерія', 'course_number' => 4],
            ['name' => 'Теорія ймовірностей та математична статистика', 'course_number' => 4],
        ];

        foreach ($subjects as $subject) {
            Subject::query()->create([
                'slug' => Str::slug($subject['name']),
                'name' => $subject['name'],
                'course_number' => $subject['course_number'],
                'description' => null,
                'image' => null,
            ]);
        }
    }
}