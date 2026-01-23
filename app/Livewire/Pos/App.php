<?php

namespace App\Livewire\Pos;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On; // Import the On attribute
use App\Models\Product; // Import the Product model
use Illuminate\Support\Facades\Http; // Import Http facade

class App extends Component
{
    protected $layout = 'components.layouts.app';

    public $outletName;
    public $cart = [];

    public function mount()
    {
        if (Auth::check() && Auth::user()->outlet) {
            $this->outletName = Auth::user()->outlet->name;
        } else {
            $this->outletName = 'No Outlet Assigned'; // Fallback
        }
    }

    #[On('productAddedToCart')]
    public function addProductToCart($productId)
    {
        $product = Product::find($productId);

        if ($product) {
            // Check if product already in cart
            if (isset($this->cart[$productId])) {
                $this->cart[$productId]['quantity']++;
            } else {
                $this->cart[$productId] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => 1,
                ];
            }
        }
    }

    public function removeItemFromCart($productId)
    {
        if (isset($this->cart[$productId])) {
            if ($this->cart[$productId]['quantity'] > 1) {
                $this->cart[$productId]['quantity']--;
            } else {
                unset($this->cart[$productId]);
            }
        }
    }

    public function getSubtotalProperty()
    {
        return array_sum(array_map(function ($item) {
            return $item['price'] * $item['quantity'];
        }, $this->cart));
    }

    public function getTotalProperty()
    {
        // For simplicity, let's assume total is subtotal for now.
        // Tax, discounts, etc., can be added here later.
        return $this->subtotal;
    }

    public function processSale()
    {
        if (empty($this->cart)) {
            session()->flash('error', 'Cart is empty. Cannot process sale.');
            return;
        }

        $items = array_map(function ($item) {
            return [
                'product_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ];
        }, array_values($this->cart));

        // For simplicity, hardcoding payment details for now.
        // A proper payment form would collect this.
        $payments = [
            [
                'amount' => $this->total,
                'payment_method' => 'Cash',
            ],
        ];

        $response = Http::withToken(Auth::user()->createToken('pos-token')->plainTextToken)
                        ->post(route('api.pos.sales'), [
                            'outlet_id' => Auth::user()->outlet_id,
                            'user_id' => Auth::id(),
                            'customer_id' => null, // Placeholder for customer selection
                            'total_amount' => $this->total,
                            'status' => 'paid', // Assuming immediate payment for simplicity
                            'items' => $items,
                            'payments' => $payments,
                        ]);

        if ($response->successful()) {
            session()->flash('success', 'Sale processed successfully!');
            $this->cart = []; // Clear cart after successful sale
        } else {
            session()->flash('error', 'Error processing sale: ' . $response->body());
        }
    }


    public function render()
    {
        return view('livewire.pos.app');
    }
}
