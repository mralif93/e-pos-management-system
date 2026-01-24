@extends('layouts.app')

@section('content')
    <div class="flex flex-col h-screen bg-gray-100 font-sans"> {{-- Lighter background, professional font --}}

        <!-- Header - Retained from previous good design -->
        <div class="p-4 bg-gray-800 text-white flex justify-between items-center z-20 shadow-lg">
            {{-- Left Section: App Info & Date/Time --}}
            <div class="flex items-center space-x-4">
                <h1 class="text-2xl font-bold tracking-tight">POS Terminal</h1> {{-- Larger, more pronounced title --}}
                <span id="current-date-time" class="text-sm opacity-80"></span> {{-- Subtle date/time --}}
            </div>

            {{-- Center Section: User & Outlet Info --}}
            <div class="flex items-center space-x-3 text-sm">
                <span class="font-semibold">{{ Auth::user()->name }}</span>
                <span class="text-gray-400">|</span>
                <span class="text-gray-300">{{ Auth::user()->outlet->name ?? 'No Outlet Assigned' }}</span>
            </div>

            {{-- Right Section: Actions --}}
            <form method="POST" action="{{ route('pos.logout') }}" id="pos-logout-form">
                @csrf
                <button type="button" onclick="confirmLogout()" class="px-4 py-2 rounded-lg bg-gray-700 hover:bg-gray-600 text-white text-sm font-medium transition duration-150 ease-in-out shadow-sm">
                    Logout
                </button>
            </form>
        </div>

        <!-- Main Content Area -->
        <div class="flex flex-grow overflow-hidden p-4 space-x-4"> {{-- Added padding and spacing --}}
            
            <!-- Left Panel: Product Search/Catalog -->
            <div class="w-2/3 flex flex-col bg-white rounded-xl shadow-lg p-6 overflow-hidden"> {{-- Card styling, more padding --}}
                <h2 class="text-2xl font-semibold text-gray-800 mb-5">Product Catalog</h2> {{-- Stronger heading --}}
                
                <div class="mb-5">
                    <input type="text" id="product-search-input" placeholder="Search products by name or SKU..." class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-lg"> {{-- Larger, better styled search --}}
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 overflow-y-auto custom-scrollbar flex-grow" id="product-list"> {{-- Responsive grid, custom scrollbar --}}
                    <!-- Products will be loaded here by JavaScript -->
                    <p class="col-span-full text-center text-gray-500 py-10">Loading products...</p>
                </div>
            </div>

            <!-- Right Panel: Cart/Payment -->
            <div class="w-1/3 flex flex-col bg-white rounded-xl shadow-lg p-6"> {{-- Card styling, more padding --}}
                <h2 class="text-2xl font-semibold text-gray-800 mb-5 border-b pb-4">Shopping Cart</h2> {{-- Stronger heading, separator --}}
                
                <div class="flex-grow overflow-y-auto custom-scrollbar mb-5" id="cart-items"> {{-- Custom scrollbar, margin --}}
                    <!-- Cart items will be loaded here by JavaScript -->
                    <p class="text-gray-500 text-center py-10">Cart is empty.</p>
                </div>

                <!-- Summary -->
                <div class="border-t pt-4 mt-auto"> {{-- Top border for summary --}}
                    <div class="flex justify-between items-center py-2 text-lg text-gray-700">
                        <span>Subtotal:</span>
                        <span id="cart-subtotal" class="font-medium">$0.00</span>
                    </div>
                    <div class="flex justify-between items-center py-2 font-bold text-2xl text-gray-900">
                        <span>Total:</span>
                        <span id="cart-total">$0.00</span>
                    </div>
                </div>
                
                <button type="button" id="process-sale-btn" onclick="posApp.redirectToCheckout()" class="w-full bg-green-600 hover:bg-green-700 text-white py-4 px-6 rounded-lg text-xl font-bold transition duration-150 ease-in-out shadow-md mt-6"> {{-- Larger, bolder button --}}
                    Process Sale
                </button>
            </div>
        </div>

        <!-- Footer -->
        <footer class="p-3 bg-gray-800 text-white text-center text-xs opacity-75 shadow-inner">
            &copy; {{ date('Y') }} My POS System. All rights reserved.
        </footer>
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
                this.updateDateTime(); // New call
                setInterval(() => this.updateDateTime(), 1000); // Update every second
            },

            getApiToken() {
                return '{{ $apiToken }}'; // Dynamically provided token
            },

            updateDateTime() {
                const now = new Date();
                const options = {
                    weekday: 'short',
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                };
                document.getElementById('current-date-time').innerText = now.toLocaleDateString('en-US', options);
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
                        <div class="bg-white rounded-lg shadow hover:shadow-md transition-shadow duration-200 cursor-pointer p-4 flex flex-col justify-between"
                             data-product-id="${product.id}"
                             data-product-name="${product.name}"
                             data-product-price="${product.price}">
                            <img src="https://via.placeholder.com/100x100?text=Product" alt="${product.name}" class="mx-auto mb-3 rounded">
                            <h3 class="font-semibold text-gray-800 text-lg mb-1">${product.name}</h3>
                            <p class="text-gray-600 text-sm mb-3">${product.description || 'No description'}</p>
                            <div class="flex justify-between items-center mt-auto">
                                <span class="font-bold text-green-600 text-xl">$${product.price.toFixed(2)}</span>
                                <button class="add-to-cart-btn bg-indigo-500 text-white text-sm px-4 py-2 rounded-lg hover:bg-indigo-600 transition-colors duration-200">Add</button>
                            </div>
                        </div>
                    `;
                    productList.innerHTML += productCard;
                });
                this.setupAddToCartButtons();
            },

            setupEventListeners() {
                document.getElementById('product-search-input').addEventListener('input', () => this.fetchProducts());
                // The process-sale-btn now uses an onclick handler directly to redirectToCheckout
                // document.getElementById('process-sale-btn').addEventListener('click', () => this.processSale());
            },

            redirectToCheckout() {
                if (this.cart.length === 0) {
                    Swal.fire('Error', 'Cart is empty. Please add items to the cart before proceeding to checkout.', 'error');
                    return;
                }
                // Redirect to the checkout page
                window.location.href = '{{ route('pos.checkout') }}';
            },

            setupAddToCartButtons() {
                document.querySelectorAll('.add-to-cart-btn').forEach(button => {
                    button.addEventListener('click', (event) => {
                        const productCard = event.target.closest('[data-product-id]');
                        const productId = productCard.dataset.productId;
                        const productName = productCard.dataset.productName;
                        const productPrice = parseFloat(productCard.dataset.productPrice);
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
                    cartItemsContainer.innerHTML = '<p class="text-gray-500 text-center py-4">Cart is empty.</p>';
                }

                let subtotal = 0;
                this.cart.forEach(item => {
                    const itemTotal = item.price * item.quantity;
                    subtotal += itemTotal;
                    const cartItem = `
                        <div class="flex items-center justify-between py-3 border-b border-gray-200 last:border-b-0">
                            <div class="flex-grow">
                                <p class="font-medium text-gray-800">${item.name}</p>
                                <p class="text-sm text-gray-600">${item.quantity} x $${item.price.toFixed(2)}</p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="font-semibold text-gray-900">$${itemTotal.toFixed(2)}</span>
                                <button data-product-id="${item.id}" class="remove-from-cart-btn text-red-500 hover:text-red-700 text-xs px-2 py-1 rounded">Remove</button>
                            </div>
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
                // This function is still here but will only be called after checkout process completion
                // or if specific API logic is needed from the main POS screen directly (unlikely with a separate checkout)
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
