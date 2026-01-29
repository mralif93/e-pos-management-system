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

        // Define Product Data for each Outlet Name
        $outletInventory = [
            'Cafe Delight' => [
                ['name' => 'Spanish Latte', 'cat' => 'Signature Coffee', 'price' => 13.90, 'cost' => 4.50],
                ['name' => 'Salted Caramel Macchiato', 'cat' => 'Signature Coffee', 'price' => 14.50, 'cost' => 5.00],
                ['name' => 'Rose Bandung Latte', 'cat' => 'Signature Coffee', 'price' => 12.90, 'cost' => 4.00],
                ['name' => 'Espresso', 'cat' => 'Espresso Bar', 'price' => 8.00, 'cost' => 2.00],
                ['name' => 'Cafe Latte', 'cat' => 'Espresso Bar', 'price' => 11.00, 'cost' => 3.50],
                ['name' => 'Matcha Latte', 'cat' => 'Tea & Refreshers', 'price' => 13.00, 'cost' => 4.50],
                ['name' => 'Butter Croissant', 'cat' => 'Pastries & Desserts', 'price' => 7.50, 'cost' => 3.00],
                ['name' => 'Grilled Chicken Chop', 'cat' => 'Main Courses', 'price' => 22.90, 'cost' => 9.00],
            ],
            'Fashion Boutique' => [
                ['name' => 'Classic White Shirt', 'cat' => 'Men\'s Wear', 'price' => 89.90, 'cost' => 30.00],
                ['name' => 'Slim Fit Jeans', 'cat' => 'Men\'s Wear', 'price' => 129.90, 'cost' => 45.00],
                ['name' => 'Summer Floral Dress', 'cat' => 'Women\'s Wear', 'price' => 159.90, 'cost' => 50.00],
                ['name' => 'Leather Handbag', 'cat' => 'Accessories', 'price' => 299.90, 'cost' => 100.00],
                ['name' => 'Canvas Sneakers', 'cat' => 'Footwear', 'price' => 99.90, 'cost' => 35.00],
            ],
            'Green Mart' => [
                ['name' => 'Organic Avocados (Pack)', 'cat' => 'Fresh Produce', 'price' => 18.90, 'cost' => 10.00],
                ['name' => 'Farm Fresh Milk 1L', 'cat' => 'Dairy & Eggs', 'price' => 8.50, 'cost' => 5.50],
                ['name' => 'Potato Chips', 'cat' => 'Snacks & Beverages', 'price' => 4.50, 'cost' => 2.00],
                ['name' => 'Laundry Detergent 3kg', 'cat' => 'Household Essentials', 'price' => 25.90, 'cost' => 15.00],
            ],
            'Tech Gadgets' => [
                ['name' => 'Smartphone X Pro', 'cat' => 'Smartphones', 'price' => 3999.00, 'cost' => 2800.00],
                ['name' => 'Ultra Slim Laptop', 'cat' => 'Laptops', 'price' => 4599.00, 'cost' => 3500.00],
                ['name' => 'Wireless Earbuds', 'cat' => 'Accessories & Peripherals', 'price' => 299.00, 'cost' => 150.00],
                ['name' => 'Smart Bulb Color', 'cat' => 'Smart Home', 'price' => 49.00, 'cost' => 20.00],
            ],
            'City Bookstore' => [
                ['name' => 'The Great Novel', 'cat' => 'Fiction', 'price' => 45.90, 'cost' => 25.00],
                ['name' => 'History of Time', 'cat' => 'Non-Fiction', 'price' => 55.90, 'cost' => 30.00],
                ['name' => 'Learning ABCs', 'cat' => 'Children\'s Books', 'price' => 15.90, 'cost' => 8.00],
                ['name' => 'Premium Notebook', 'cat' => 'Stationery', 'price' => 25.90, 'cost' => 10.00],
            ]
        ];

        foreach ($outlets as $outlet) {
            $items = $outletInventory[$outlet->name] ?? [];

            foreach ($items as $item) {
                $category = $categories->where('name', $item['cat'])->first();
                if (!$category)
                    continue;

                // Check if product exists globally (by name)
                $product = Product::firstOrCreate([
                    'name' => $item['name']
                ], [
                    'category_id' => $category->id,
                    'slug' => Str::slug($item['name']),
                    'sku' => Str::upper(Str::random(8)), // Unique SKU
                    'barcode' => Str::random(12), // Dummy Barcode
                    'description' => 'Description for ' . $item['name'],
                    'price' => $item['price'], // Base price
                    'cost' => $item['cost'],
                    'stock_level' => 100,
                    'is_active' => true,
                    'has_variants' => false,
                ]);

                // Assign outlet specific price
                $product->prices()->updateOrCreate([
                    'outlet_id' => $outlet->id
                ], [
                    'price' => $item['price'],
                    'stock_level' => 100
                ]);
            }
        }
    }
}
