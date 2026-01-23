<div class="flex flex-col h-screen bg-gray-200">
        <!-- Header -->
        <div class="p-4 bg-gray-800 text-white flex justify-between items-center z-20 shadow-md">
            <h1 class="text-xl font-bold">POS Terminal - {{ $outletName }}</h1>
            <form method="POST" action="{{ route('pos.logout') }}" id="pos-logout-form">
                @csrf
                <button type="button" onclick="confirmLogout()" class="text-gray-300 hover:text-white text-sm p-2 rounded-md bg-gray-700 hover:bg-gray-600 transition duration-150 ease-in-out">Logout</button>
            </form>
        </div>

        <div class="flex flex-grow">
            <!-- Left Panel: Product Search/Catalog -->
            <div class="w-2/3 p-4 bg-white shadow-lg overflow-y-auto">
                <h2 class="text-2xl font-bold mb-4">Product Catalog</h2>
                @livewire('pos.product-search')
            </div>

            <!-- Right Panel: Cart/Payment -->
            <div class="w-1/3 p-4 bg-gray-100 shadow-lg flex flex-col">
                <h2 class="text-2xl font-bold mb-4">Shopping Cart</h2>
                <div class="flex-grow bg-white p-4 rounded-lg shadow mb-4 overflow-y-auto">
                    @forelse($cart as $productId => $item)
                        <div class="flex justify-between items-center py-2 border-b">
                            <span>{{ $item['name'] }} x {{ $item['quantity'] }}</span>
                            <span>${{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                            <button wire:click="removeItemFromCart({{ $productId }})" class="text-red-500 hover:text-red-700 text-sm ml-2">Remove</button>
                        </div>
                    @empty
                        <p class="text-gray-500">Cart is empty.</p>
                    @endforelse
                </div>
                <!-- Subtotal and Total -->
                <div class="mb-4">
                    <div class="flex justify-between items-center py-2 text-lg">
                        <span>Subtotal:</span>
                        <span>${{ number_format($this->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 font-bold text-xl">
                        <span>Total:</span>
                        <span>${{ number_format($this->total, 2) }}</span>
                    </div>
                </div>
                
                {{-- Payment Component will go here --}}
                <button wire:click="processSale" class="w-full bg-green-500 text-white py-3 px-4 rounded-lg hover:bg-green-600 text-xl font-semibold">Process Sale</button>
            </div>
        </div>
    </div>
