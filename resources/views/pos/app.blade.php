@extends('layouts.app')

@section('content')
    <div class="flex flex-col h-screen bg-gray-50 font-sans antialiased text-gray-800"> {{-- Lighter background, professional font, anti-aliased text --}}

        <!-- Header -->
        <header class="p-5 bg-gradient-to-r from-gray-900 to-gray-700 text-white flex justify-between items-center z-20 shadow-xl">
            {{-- Left Section: App Info & Date/Time --}}
            <div class="flex items-center space-x-5">
                <h1 class="text-3xl font-extrabold tracking-wide">POS Terminal</h1> {{-- Larger, bolder, wider tracking title --}}
                <span id="current-date-time" class="text-base font-light opacity-80 border-l border-gray-600 pl-4"></span> {{-- Subtle date/time with separator --}}
            </div>

            {{-- Center Section: User & Outlet Info --}}
            <div class="flex items-center space-x-4 text-base">
                <span class="font-semibold text-gray-200">{{ Auth::user()->name }}</span>
                <span class="text-gray-400">|</span>
                <span class="font-medium text-gray-300">{{ Auth::user()->outlet->name ?? 'No Outlet Assigned' }}</span>
            </div>

            {{-- Right Section: Actions --}}
            <form method="POST" action="{{ route('pos.logout') }}" id="pos-logout-form">
                @csrf
                <button type="button" onclick="confirmLogout()" class="px-5 py-2 rounded-full bg-red-600 hover:bg-red-700 text-white text-base font-medium transition duration-200 ease-in-out shadow-md">
                    Logout
                </button>
            </form>
        </header>

        <!-- Main Content Area -->
        <div class="flex flex-grow h-full overflow-hidden p-6 space-x-6"> {{-- Increased padding and spacing --}}
            
            <!-- Left Panel: Product Search/Catalog -->
            <div class="w-2/3 flex flex-col bg-white rounded-2xl shadow-2xl p-7 overflow-hidden"> {{-- More rounded corners, stronger shadow, more padding --}}
                <h2 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-4">Product Catalog</h2> {{-- Stronger heading --}}
                
                <div class="mb-6 relative">
                    <input type="text" id="product-search-input" placeholder="Search products by name or SKU..." class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl shadow-inner focus:outline-none focus:ring-3 focus:ring-indigo-400 focus:border-indigo-400 text-lg transition-all duration-200"> {{-- Larger, better styled search with space for icon --}}
                    <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg> {{-- Search Icon --}}
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 overflow-y-auto custom-scrollbar flex-grow" id="product-list"> {{-- More gap, responsive grid, custom scrollbar --}}
                    <!-- Products will be loaded here by JavaScript -->
                    <p class="col-span-full text-center text-gray-500 py-20 text-lg">No products found. Start searching!</p>
                </div>
            </div>

            <!-- Right Panel: Cart/Payment -->
            <div class="w-1/3 flex flex-col bg-white rounded-2xl shadow-2xl p-7"> {{-- More rounded corners, stronger shadow, more padding --}}
                <h2 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-4">Shopping Cart</h2> {{-- Stronger heading, separator --}}
                
                <div class="flex-grow overflow-y-auto custom-scrollbar mb-6" id="cart-items"> {{-- Custom scrollbar, increased margin --}}
                    <!-- Cart items will be loaded here by JavaScript -->
                    <p class="text-gray-500 text-center py-20 text-lg">Your cart is empty. Add some products!</p>
                </div>

                <!-- Summary -->
                <div class="border-t border-gray-200 pt-5 mt-auto"> {{-- Top border for summary, increased padding --}}
                    <div class="flex justify-between items-center py-3 text-xl text-gray-700">
                        <span class="font-medium">Subtotal:</span>
                        <span id="cart-subtotal" class="font-bold text-gray-900">$0.00</span>
                    </div>
                    <div class="flex justify-between items-center py-3 font-extrabold text-3xl text-green-700"> {{-- Larger, bolder, green total --}}
                        <span>Total:</span>
                        <span id="cart-total">$0.00</span>
                    </div>
                </div>
                
                <button type="button" id="process-sale-btn" onclick="posApp.redirectToCheckout()" class="w-full bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-500/50 text-white py-5 px-8 rounded-xl text-2xl font-extrabold transition duration-200 ease-in-out shadow-lg transform hover:-translate-y-1 mt-8"> {{-- Larger, bolder, more interactive button --}}
                    Process Sale
                </button>
            </div>
        </div>

        <!-- Footer -->
        <footer class="p-4 bg-gray-900 text-white text-center text-sm opacity-90 shadow-inner"> {{-- Darker, more prominent footer --}}
            &copy; {{ date('Y') }} Khushboo Food App. All rights reserved.
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
                    productList.innerHTML = '<p class="col-span-full text-center text-gray-500 py-20 text-lg">No products found. Start searching!</p>'; // Updated empty state
                    return;
                }

                this.products.forEach(product => {
                    const productCard = `
                        <div class="bg-white border border-gray-200 rounded-xl shadow-md hover:shadow-xl transition-all duration-200 cursor-pointer p-5 flex flex-col justify-between transform hover:-translate-y-1"
                             data-product-id="${product.id}"
                             data-product-name="${product.name}"
                             data-product-price="${product.price}">
                            <img src="https://via.placeholder.com/150x150?text=Product" alt="${product.name}" class="mx-auto mb-4 rounded-lg object-cover w-full h-32"> {{-- Larger image, object-cover --}}
                            <h3 class="font-bold text-gray-900 text-xl mb-2">${product.name}</h3> {{-- Bolder, larger name --}}
                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">${product.description || 'No description available.'}</p> {{-- Multi-line clamp --}}
                            <div class="flex justify-between items-center mt-auto pt-3 border-t border-gray-100">
                                <span class="font-extrabold text-green-700 text-2xl">$${product.price.toFixed(2)}</span> {{-- Larger, bolder price --}}
                                <button class="add-to-cart-btn bg-indigo-600 text-white text-base px-5 py-2 rounded-lg hover:bg-indigo-700 transition-colors duration-200 shadow-md">Add</button> {{-- Bigger, bolder Add button --}}
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
                    cartItemsContainer.innerHTML = '<p class="text-gray-500 text-center py-10 text-lg">Your cart is empty. Add some products!</p>'; // Updated empty state
                    return;
                }

                let subtotal = 0;
                this.cart.forEach(item => {
                    const itemTotal = item.price * item.quantity;
                    subtotal += itemTotal;
                    const cartItem = `
                        <div class="flex items-center justify-between py-4 border-b border-gray-100 last:border-b-0"> {{-- Increased padding, lighter border --}}
                            <div class="flex-grow pr-4"> {{-- Added right padding --}}
                                <p class="font-semibold text-gray-900 text-lg">${item.name}</p> {{-- Bolder, larger name --}}
                                <p class="text-sm text-gray-600">${item.quantity} x $${item.price.toFixed(2)}</p>
                            </div>
                            <div class="flex items-center space-x-3">
                                <span class="font-bold text-gray-900 text-xl">$${itemTotal.toFixed(2)}</span> {{-- Larger, bolder total --}}
                                <button data-product-id="${item.id}" class="remove-from-cart-btn text-red-500 hover:text-red-700 p-2 rounded-full hover:bg-red-50 transition-colors duration-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg> {{-- Trash icon --}}
                                </button>
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
                        const productId = event.target.closest('button').dataset.productId; // Find the button itself
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
                        'Authorization': 'Bearer ' + this.apiToken
                    }
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