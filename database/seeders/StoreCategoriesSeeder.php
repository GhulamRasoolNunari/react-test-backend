<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class StoreCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::where('source', 'news-api')->delete();
        $categories = [
            ['name' => 'business', 'source' => 'news-api', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'entertainment', 'source' => 'news-api', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'general', 'source' => 'news-api', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'health', 'source' => 'news-api', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'science', 'source' => 'news-api', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'sports', 'source' => 'news-api', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'technology', 'source' => 'news-api', 'created_at' => now(), 'updated_at' => now()]
        ];
        Category::insert($categories);
    }
}
