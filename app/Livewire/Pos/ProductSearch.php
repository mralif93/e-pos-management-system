<?php

namespace App\Livewire\Pos;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ProductSearch extends Component
{
    public $query = '';
    public $products = [];

    public function updatedQuery()
    {
        if (empty($this->query)) {
            $this->products = [];
            return;
        }

        // Call the backend API for product search
        $response = Http::withToken(Auth::user()->createToken('pos-token')->plainTextToken)
                        ->get(route('api.pos.products', ['query' => $this->query]));

        if ($response->successful()) {
            $this->products = $response->json();
        } else {
            $this->products = [];
            // Log error or show a notification
        }
    }

    public function addProductToCart($productId)
    {
        $this->dispatch('productAddedToCart', $productId);
    }

    public function render()
    {
        return view('livewire.pos.product-search');
    }
}
