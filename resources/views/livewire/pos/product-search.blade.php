<div>
    <input type="text" wire:model.live="query" placeholder="Search products..." class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">

    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($products as $product)
            <div class="bg-gray-100 p-4 rounded-lg shadow">
                <h3 class="font-semibold text-lg">{{ $product['name'] }}</h3>
                <p class="text-gray-600">${{ number_format($product['price'], 2) }}</p>
                <button wire:click="addProductToCart({{ $product['id'] }})" class="mt-2 w-full bg-blue-500 text-white py-1 px-3 rounded hover:bg-blue-600">Add to Cart</button>
            </div>
        @empty
            <p class="col-span-full text-center text-gray-500">No products found.</p>
        @endforelse
    </div>
</div>