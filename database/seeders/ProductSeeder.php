<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Outlet;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();
        $outlets = Outlet::all();

        $mainOutlet = $outlets->where('name', 'Main Outlet')->first();
        $secondOutlet = $outlets->where('name', 'Second Outlet')->first();
        if ($mainOutlet) {
            $cafeItems = [
                // Signature Coffee
                ['name' => 'Spanish Latte', 'cat' => 'Signature Coffee', 'price' => 13.90, 'cost' => 4.50],
                ['name' => 'Salted Caramel Macchiato', 'cat' => 'Signature Coffee', 'price' => 14.50, 'cost' => 5.00],
                ['name' => 'Hazelnut Mocha', 'cat' => 'Signature Coffee', 'price' => 14.50, 'cost' => 5.00],
                ['name' => 'Rose Bandung Latte', 'cat' => 'Signature Coffee', 'price' => 12.90, 'cost' => 4.00],

                // Espresso Bar
                ['name' => 'Espresso', 'cat' => 'Espresso Bar', 'price' => 8.00, 'cost' => 2.00],
                ['name' => 'Americano', 'cat' => 'Espresso Bar', 'price' => 9.00, 'cost' => 2.20],
                ['name' => 'Cappuccino', 'cat' => 'Espresso Bar', 'price' => 11.00, 'cost' => 3.50],
                ['name' => 'Cafe Latte', 'cat' => 'Espresso Bar', 'price' => 11.00, 'cost' => 3.50],
                ['name' => 'Flat White', 'cat' => 'Espresso Bar', 'price' => 11.00, 'cost' => 3.50],

                // Tea & Refreshers
                ['name' => 'Matcha Latte', 'cat' => 'Tea & Refreshers', 'price' => 13.00, 'cost' => 4.50],
                ['name' => 'Iced Peach Tea', 'cat' => 'Tea & Refreshers', 'price' => 9.90, 'cost' => 2.50],
                ['name' => 'Lemon Earl Grey', 'cat' => 'Tea & Refreshers', 'price' => 9.90, 'cost' => 2.50],
                ['name' => 'Chocolate Frappe', 'cat' => 'Tea & Refreshers', 'price' => 15.00, 'cost' => 5.00],

                // Local Favorites
                ['name' => 'Nasi Lemak Special', 'cat' => 'Local Favorites', 'price' => 15.90, 'cost' => 6.00],
                ['name' => 'Mee Siam w/ Rendang', 'cat' => 'Local Favorites', 'price' => 16.90, 'cost' => 7.00],
                ['name' => 'Hainanese Chicken Rice', 'cat' => 'Local Favorites', 'price' => 14.90, 'cost' => 6.00],
                ['name' => 'Curry Laksa', 'cat' => 'Local Favorites', 'price' => 15.90, 'cost' => 6.50],

                // Pastries & Desserts
                ['name' => 'Butter Croissant', 'cat' => 'Pastries & Desserts', 'price' => 7.50, 'cost' => 3.00],
                ['name' => 'Pain Au Chocolat', 'cat' => 'Pastries & Desserts', 'price' => 8.50, 'cost' => 3.50],
                ['name' => 'Burnt Cheesecake Slice', 'cat' => 'Pastries & Desserts', 'price' => 16.00, 'cost' => 6.00],
                ['name' => 'Cinnamon Roll', 'cat' => 'Pastries & Desserts', 'price' => 6.50, 'cost' => 2.50],

                // Main Courses
                ['name' => 'Grilled Chicken Chop', 'cat' => 'Main Courses', 'price' => 22.90, 'cost' => 9.00],
                ['name' => 'Spaghetti Carbonara', 'cat' => 'Main Courses', 'price' => 19.90, 'cost' => 7.00],
                ['name' => 'Fish & Chips', 'cat' => 'Main Courses', 'price' => 21.90, 'cost' => 8.50],
                ['name' => 'Beef Bolognese', 'cat' => 'Main Courses', 'price' => 18.90, 'cost' => 7.00],
            ];

            foreach ($cafeItems as $item) {
                $category = $categories->where('name', $item['cat'])->first();
                if (!$category)
                    continue;

                $prod = Product::create([
                    'category_id' => $category->id,
                    'name' => $item['name'],
                    'slug' => Str::slug($item['name']),
                    'description' => 'Freshly prepared ' . $item['name'],
                    'price' => $item['price'],
                    'cost' => $item['cost'],
                    'stock_level' => 100,
                    'is_active' => true,
                    'has_variants' => false,
                ]);

                $prod->prices()->create([
                    'outlet_id' => $mainOutlet->id,
                    'price' => $item['price'],
                    'stock_level' => 100
                ]);
            }
        }
    }
}
