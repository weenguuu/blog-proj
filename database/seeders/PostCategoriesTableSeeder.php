<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PostCategory;

class PostCategoriesTableSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Новости'],
            ['name' => 'ШоуБиз'],
            ['name' => 'Спорт']
        ];

        foreach ($categories as $category) {
            PostCategory::create($category);
        }
    }
}
