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
        $categories = [
            // Cafe Categories
            'Signature Coffee',
            'Espresso Bar',
            'Tea & Refreshers',
            'Pastries & Desserts',
            'Main Courses',

            // Fashion Categories
            'Men\'s Wear',
            'Women\'s Wear',
            'Accessories',
            'Footwear',

            // Grocery Categories
            'Fresh Produce',
            'Dairy & Eggs',
            'Snacks & Beverages',
            'Household Essentials',

            // Tech Categories
            'Smartphones',
            'Laptops',
            'Accessories & Peripherals',
            'Smart Home',

            // Bookstore Categories
            'Fiction',
            'Non-Fiction',
            'Children\'s Books',
            'Stationery',
        ];

        foreach ($categories as $name) {
            Category::firstOrCreate([
                'name' => $name,
            ], [
                'slug' => Str::slug($name),
            ]);
        }
    }
}
