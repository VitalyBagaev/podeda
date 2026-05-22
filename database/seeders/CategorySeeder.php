<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'ИТ-поддержка и ПО'],
            ['name' => 'Производство и технологии'],
            ['name' => 'Логистика и транспорт'],
            ['name' => 'Охрана труда и промбезопасность'],
            ['name' => 'Кадры и обучение'],
            ['name' => 'Закупки и снабжение'],
            ['name' => 'Бухгалтерия и отчётность'],
            ['name' => 'Связь и инфраструктура'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(['name' => $category['name']], $category);
        }
    }
}
