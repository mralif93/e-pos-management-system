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

        // Product 1
        $product1 = Product::create([
            'category_id' => $categories->where('name', 'Electronics')->first()->id,
            'name' => 'Smartphone X',
            'slug' => Str::slug('Smartphone X'),
            'description' => 'A powerful smartphone with advanced features.',
            'price' => 999.99,
            'cost' => 700.00,
            'stock_level' => 100,
            'is_active' => true,
            'has_variants' => false, // Ensure this is set
        ]);
        foreach ($outlets as $outlet) {
            $product1->prices()->create([
                'outlet_id' => $outlet->id,
                'price' => 999.99,
                'stock_level' => rand(5, 50)
            ]);
        }

        // Product 2
        $product2 = Product::create([
            'category_id' => $categories->where('name', 'Clothing')->first()->id,
            'name' => 'T-Shirt Pro',
            'slug' => Str::slug('T-Shirt Pro'),
            'description' => 'Comfortable and stylish t-shirt.',
            'price' => 29.99,
            'cost' => 15.00,
            'stock_level' => 200,
            'is_active' => true,
            'has_variants' => false,
        ]);
        foreach ($outlets as $outlet) {
            $product2->prices()->create([
                'outlet_id' => $outlet->id,
                'price' => 29.99,
                'stock_level' => rand(10, 100)
            ]);
        }

        // Product 3
        $product3 = Product::create([
            'category_id' => $categories->where('name', 'Books')->first()->id,
            'name' => 'The Great Novel',
            'slug' => Str::slug('The Great Novel'),
            'description' => 'A captivating novel everyone should read.',
            'price' => 19.50,
            'cost' => 10.00,
            'stock_level' => 50,
            'is_active' => true,
            'has_variants' => false,
        ]);
        foreach ($outlets as $outlet) {
            $product3->prices()->create([
                'outlet_id' => $outlet->id,
                'price' => 19.50,
                'stock_level' => rand(2, 20)
            ]);
        }
    }
}
