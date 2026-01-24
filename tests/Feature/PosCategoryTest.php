<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Outlet;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductOutletPrice;

class PosCategoryTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $outlet;

    protected function setUp(): void
    {
        parent::setUp();

        $this->outlet = Outlet::create(['name' => 'Test Outlet', 'address' => 'Test Address']);
        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'outlet_id' => $this->outlet->id,
            'role' => 'admin',
        ]);
    }

    public function test_can_fetch_categories()
    {
        Category::create(['name' => 'Coffee', 'slug' => 'coffee', 'sort_order' => 1]);
        Category::create(['name' => 'Food', 'slug' => 'food', 'sort_order' => 2]);

        $response = $this->actingAs($this->user)
            ->getJson(route('api.pos.categories'));

        $response->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonFragment(['name' => 'Coffee']);
    }

    public function test_can_filter_products_by_category()
    {
        $category1 = Category::create(['name' => 'Coffee', 'slug' => 'coffee']);
        $category2 = Category::create(['name' => 'Food', 'slug' => 'food']);

        $product1 = Product::create([
            'category_id' => $category1->id,
            'name' => 'Latte',
            'slug' => 'latte',
            'price' => 10,
            'stock_level' => 100
        ]);
        $product2 = Product::create([
            'category_id' => $category2->id,
            'name' => 'Sandwich',
            'slug' => 'sandwich',
            'price' => 15,
            'stock_level' => 100
        ]);

        // Assign prices
        ProductOutletPrice::create(['product_id' => $product1->id, 'outlet_id' => $this->outlet->id, 'price' => 10]);
        ProductOutletPrice::create(['product_id' => $product2->id, 'outlet_id' => $this->outlet->id, 'price' => 15]);

        // Filter by Category 1
        $response = $this->actingAs($this->user)
            ->getJson(route('api.pos.products', ['category_id' => $category1->id]));

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment(['name' => 'Latte'])
            ->assertJsonMissing(['name' => 'Sandwich']);
    }
}
