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

        // Product 1 - Main Outlet ONLY
        $product1 = Product::create([
            'category_id' => $categories->where('name', 'Electronics')->first()->id,
            'name' => 'Smartphone X (Main Only)',
            'slug' => Str::slug('Smartphone X'),
            'description' => 'A powerful smartphone available only at Main Outlet.',
            'price' => 999.99,
            'cost' => 700.00,
            'stock_level' => 100,
            'is_active' => true,
            'has_variants' => false,
        ]);

        $mainOutlet = $outlets->where('name', 'Main Outlet')->first();
        if ($mainOutlet) {
            $product1->prices()->create([
                'outlet_id' => $mainOutlet->id,
                'price' => 999.99,
                'stock_level' => 50
            ]);
        }

        // Product 2 - Second Outlet ONLY
        $product2 = Product::create([
            'category_id' => $categories->where('name', 'Clothing')->first()->id,
            'name' => 'T-Shirt Pro (Second Only)',
            'slug' => Str::slug('T-Shirt Pro'),
            'description' => 'Exclusive t-shirt for Second Outlet.',
            'price' => 29.99,
            'cost' => 15.00,
            'stock_level' => 200,
            'is_active' => true,
            'has_variants' => false,
        ]);

        $secondOutlet = $outlets->where('name', 'Second Outlet')->first();
        if ($secondOutlet) {
            $product2->prices()->create([
                'outlet_id' => $secondOutlet->id,
                'price' => 29.99,
                'stock_level' => 100
            ]);
        }

        // Product 3 - BOTH Outlets (Different Prices)
        $product3 = Product::create([
            'category_id' => $categories->where('name', 'Books')->first()->id,
            'name' => 'The Great Novel (Both)',
            'slug' => Str::slug('The Great Novel'),
            'description' => 'A bestseller available everywhere.',
            'price' => 19.50, // Base price
            'cost' => 10.00,
            'stock_level' => 50,
            'is_active' => true,
            'has_variants' => false,
        ]);

        if ($mainOutlet) {
            $product3->prices()->create([
                'outlet_id' => $mainOutlet->id,
                'price' => 19.50, // Standard price
                'stock_level' => 20
            ]);
        }

        if ($secondOutlet) {
            $product3->prices()->create([
                'outlet_id' => $secondOutlet->id,
                'price' => 25.00, // Premium price at Second Outlet
                'stock_level' => 5
            ]);
        }
    }
}
