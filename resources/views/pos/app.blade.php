@extends('layouts.app')

@section('content')
    <div class="flex flex-col h-screen bg-gray-200">
        <!-- Header -->
        <div class="p-4 bg-gray-800 text-white flex justify-between items-center z-20 shadow-md">
            <h1 class="text-xl font-bold">POS Terminal - {{ Auth::user()->name }} - {{ Auth::user()->outlet->name ?? 'No Outlet Assigned' }}</h1>
            <form method="POST" action="{{ route('pos.logout') }}" id="pos-logout-form">
                @csrf
                <button type="button" onclick="confirmLogout()" class="text-gray-300 hover:text-white text-sm p-2 rounded-md bg-gray-700 hover:bg-gray-600 transition duration-150 ease-in-out">Logout</button>
            </form>
        </div>

        <div class="flex flex-grow">
            <!-- Left Panel: Product Search/Catalog -->
            <div class="w-2/3 p-4 bg-white shadow-lg overflow-y-auto" id="product-catalog">
                <h2 class="text-2xl font-bold mb-4">Product Catalog</h2>
                <input type="text" id="product-search-input" placeholder="Search products..." class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">

                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="product-list">
                    <!-- Products will be loaded here by JavaScript -->
                    <p class="col-span-full text-center text-gray-500">Loading products...</p>
                </div>
            </div>

            <!-- Right Panel: Cart/Payment -->
            <div class="w-1/3 p-4 bg-gray-100 shadow-lg flex flex-col" id="pos-cart">
                <h2 class="text-2xl font-bold mb-4">Shopping Cart</h2>
                <div class="flex-grow bg-white p-4 rounded-lg shadow mb-4 overflow-y-auto" id="cart-items">
                    <!-- Cart items will be loaded here by JavaScript -->
                    <p class="text-gray-500">Cart is empty.</p>
                </div>
                <!-- Subtotal and Total -->
                <div class="mb-4">
                    <div class="flex justify-between items-center py-2 text-lg">
                        <span>Subtotal:</span>
                        <span id="cart-subtotal">$0.00</span>
                    </div>
                    <div class="flex justify-between items-center py-2 font-bold text-xl">
                        <span>Total:</span>
                        <span id="cart-total">$0.00</span>
                    </div>
                </div>
                
                <button id="process-sale-btn" class="w-full bg-green-500 text-white py-3 px-4 rounded-lg hover:bg-green-600 text-xl font-semibold">Process Sale</button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // All JavaScript for POS functionality will go here
        const posApp = {
            apiToken: null,
            products: [],
            cart: [],
            init() {
                this.apiToken = this.getApiToken(); // Function to get API token
                this.productSearchInput = document.getElementById('product-search-input'); // Add this line
                this.fetchProducts();
                this.setupEventListeners();
            },

            getApiToken() {
                return '{{ $apiToken }}'; // Dynamically provided token
            },

            fetchProducts() {
                fetch('{{ route('api.pos.products') }}?query=' + this.productSearchInput.value, {
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': 'Bearer ' + this.apiToken
                    }
                })
                .then(response => response.json())
                .then(data => {
                    this.products = data;
                    this.renderProducts();
                })
                .catch(error => console.error('Error fetching products:', error));
            },

            renderProducts() {
                const productList = document.getElementById('product-list');
                productList.innerHTML = ''; // Clear previous products

                if (this.products.length === 0) {
                    productList.innerHTML = '<p class="col-span-full text-center text-gray-500">No products found.</p>';
                    return;
                }

                this.products.forEach(product => {
                    const productCard = `
                        <div class="bg-gray-100 p-4 rounded-lg shadow">
                            <h3 class="font-semibold text-lg">${product.name}</h3>
                            <p class="text-gray-600">$${product.price.toFixed(2)}</p>
                            <button data-product-id="${product.id}"
                                    data-product-name="${product.name}"
                                    data-product-price="${product.price}"
                                    class="mt-2 w-full bg-blue-500 text-white py-1 px-3 rounded hover:bg-blue-600 add-to-cart-btn">Add to Cart</button>
                        </div>
                    `;
                    productList.innerHTML += productCard;
                });
                this.setupAddToCartButtons();
            },

            setupEventListeners() {
                document.getElementById('product-search-input').addEventListener('input', () => this.fetchProducts());
                document.getElementById('process-sale-btn').addEventListener('click', () => this.processSale());
            },

            setupAddToCartButtons() {
                document.querySelectorAll('.add-to-cart-btn').forEach(button => {
                    button.addEventListener('click', (event) => {
                        const productId = event.target.dataset.productId;
                        const productName = event.target.dataset.productName;
                        const productPrice = parseFloat(event.target.dataset.productPrice);
                        this.addToCart(productId, productName, productPrice);
                    });
                });
            },

            addToCart(productId, productName, productPrice) {
                const existingItemIndex = this.cart.findIndex(item => item.id == productId);

                if (existingItemIndex > -1) {
                    this.cart[existingItemIndex].quantity++;
                } else {
                    this.cart.push({
                        id: productId,
                        name: productName,
                        price: productPrice,
                        quantity: 1,
                    });
                }
                this.renderCart();
            },

            removeItemFromCart(productId) {
                const existingItemIndex = this.cart.findIndex(item => item.id == productId);

                if (existingItemIndex > -1) {
                    if (this.cart[existingItemIndex].quantity > 1) {
                        this.cart[existingItemIndex].quantity--;
                    } else {
                        this.cart.splice(existingItemIndex, 1);
                    }
                }
                this.renderCart();
            },

            renderCart() {
                const cartItemsContainer = document.getElementById('cart-items');
                cartItemsContainer.innerHTML = ''; // Clear previous cart items

                if (this.cart.length === 0) {
                    cartItemsContainer.innerHTML = '<p class="text-gray-500">Cart is empty.</p>';
                }

                let subtotal = 0;
                this.cart.forEach(item => {
                    const itemTotal = item.price * item.quantity;
                    subtotal += itemTotal;
                    const cartItem = `
                        <div class="flex justify-between items-center py-2 border-b">
                            <span>${item.name} x ${item.quantity}</span>
                            <span>$${itemTotal.toFixed(2)}</span>
                            <button data-product-id="${item.id}" class="text-red-500 hover:text-red-700 text-sm ml-2 remove-from-cart-btn">Remove</button>
                        </div>
                    `;
                    cartItemsContainer.innerHTML += cartItem;
                });

                document.getElementById('cart-subtotal').innerText = `$${subtotal.toFixed(2)}`;
                document.getElementById('cart-total').innerText = `$${subtotal.toFixed(2)}`; // For now, total is subtotal
                this.setupRemoveFromCartButtons();
            },

            setupRemoveFromCartButtons() {
                document.querySelectorAll('.remove-from-cart-btn').forEach(button => {
                    button.addEventListener('click', (event) => {
                        const productId = event.target.dataset.productId;
                        this.removeItemFromCart(productId);
                    });
                });
            },

            processSale() {
                if (this.cart.length === 0) {
                    Swal.fire('Error', 'Cart is empty. Cannot process sale.', 'error');
                    return;
                }

                // Prepare items for API
                const items = this.cart.map(item => ({
                    product_id: item.id,
                    quantity: item.quantity,
                    price: item.price,
                }));

                // For simplicity, payment method is hardcoded to Cash
                const payments = [{
                    amount: parseFloat(document.getElementById('cart-total').innerText.replace('$', '')),
                    payment_method: 'Cash',
                }];

                fetch('{{ route('api.pos.sales') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + this.apiToken,
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' // Laravel CSRF token
                    },
                    body: JSON.stringify({
                        outlet_id: {{ Auth::user()->outlet_id ?? 'null' }},
                        user_id: {{ Auth::id() ?? 'null' }},
                        customer_id: null, // Placeholder for customer selection
                        total_amount: parseFloat(document.getElementById('cart-total').innerText.replace('$', '')),
                        status: 'paid', // Assuming immediate payment
                        items: items,
                        payments: payments,
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.message === 'Sale processed successfully') {
                        Swal.fire('Success', 'Sale processed successfully!', 'success');
                        this.cart = [];
                        this.renderCart(); // Clear cart display
                    } else {
                        Swal.fire('Error', data.message || 'Error processing sale.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error processing sale:', error);
                    Swal.fire('Error', 'An unexpected error occurred.', 'error');
                });
            }
        };

        // Initialize the POS application
        document.addEventListener('DOMContentLoaded', () => {
            posApp.init();
        });
    </script>
    @endpush
