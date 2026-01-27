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
            'name' => 'Signature Coffee',
            'slug' => Str::slug('Signature Coffee'),
        ]);

        Category::create([
            'name' => 'Espresso Bar',
            'slug' => Str::slug('Espresso Bar'),
        ]);

        Category::create([
            'name' => 'Tea & Refreshers',
            'slug' => Str::slug('Tea & Refreshers'),
        ]);

        Category::create([
            'name' => 'Local Favorites',
            'slug' => Str::slug('Local Favorites'),
        ]);

        Category::create([
            'name' => 'Pastries & Desserts',
            'slug' => Str::slug('Pastries & Desserts'),
        ]);

        Category::create([
            'name' => 'Main Courses',
            'slug' => Str::slug('Main Courses'),
        ]);
    }
}
