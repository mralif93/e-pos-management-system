<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create([
            'name' => 'Electronics',
            'slug' => Str::slug('Electronics'),
        ]);

        Category::create([
            'name' => 'Clothing',
            'slug' => Str::slug('Clothing'),
        ]);

        Category::create([
            'name' => 'Books',
            'slug' => Str::slug('Books'),
        ]);
    }
}
