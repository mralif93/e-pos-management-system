@extends('layouts.app')

@section('content')
    <div class="flex flex-col h-screen bg-slate-100 font-sans antialiased text-slate-800 overflow-hidden">

        <style>
            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(10px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .animate-fade-in {
                animation: fadeIn 0.5s ease-out forwards;
            }

            .custom-scrollbar::-webkit-scrollbar {
                width: 6px;
            }

            .custom-scrollbar::-webkit-scrollbar-track {
                background: #f1f5f9;
            }

            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 10px;
            }

            .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background: #94a3b8;
            }
        </style>

        <!-- Top Navigation Bar -->
        <header
            class="h-16 bg-white border-b border-slate-200 flex justify-between items-center px-6 z-30 shadow-sm flex-shrink-0">
            {{-- Brand & Time --}}
            <div class="flex items-center gap-6">
                <div class="flex items-center gap-2">
                    <div
                        class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-bold text-lg shadow-indigo-200 shadow-md">
                        P
                    </div>
                    <h1 class="text-xl font-bold tracking-tight text-slate-900">POS Terminal</h1>
                </div>
                <div class="h-6 w-px bg-slate-300 mx-2"></div>
                <div class="flex flex-col">
                    <span id="current-date-time" class="text-sm font-medium text-slate-500 font-mono tracking-wide"></span>
                </div>
            </div>

            {{-- User Profile & Actions --}}
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-3 px-3 py-1.5 bg-slate-50 rounded-full border border-slate-200">
                    <div
                        class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-sm">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div class="flex flex-col pr-2">
                        <span class="text-xs font-bold text-slate-700 leading-tight">{{ Auth::user()->name }}</span>
                        <span class="text-[10px] font-medium text-slate-500 uppercase tracking-wider leading-tight">
                            {{ Auth::user()->outlet->name ?? 'No Outlet' }}
                        </span>
                    </div>
                </div>

                <form method="POST" action="{{ route('pos.logout') }}" id="pos-logout-form">
                    @csrf
                    <button type="button" onclick="confirmLogout()"
                        class="group flex items-center justify-center w-10 h-10 rounded-full bg-white border border-slate-200 text-slate-500 hover:bg-red-50 hover:text-red-500 hover:border-red-200 transition-all duration-200 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5 transform group-hover:translate-x-0.5 transition-transform" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>
            </div>
        </header>

        <!-- Main Workspace -->
        <main class="flex-grow flex overflow-hidden p-4 gap-4 animate-fade-in">

            <!-- Left Panel: Catalog -->
            <section
                class="flex-grow flex flex-col bg-white rounded-2xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] ring-1 ring-black/5 overflow-hidden w-2/3">

                <!-- Search & Filters -->
                <div class="p-5 border-b border-slate-100 bg-white z-20">
                    <div class="relative max-w-2xl">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" id="product-search-input"
                            class="block w-full pl-12 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl leading-5 placeholder-slate-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-200 sm:text-sm"
                            placeholder="Search by product name, SKU, or barcode...">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <span class="text-xs text-slate-400 border border-slate-200 rounded px-1.5 py-0.5">⌘K</span>
                        </div>
                    </div>
                </div>

                <!-- Product Grid -->
                <div id="product-list"
                    class="flex-grow overflow-y-auto p-5 custom-scrollbar grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-5 content-start bg-slate-50/50">
                    <!-- Javascript will populate this -->
                </div>
            </section>

            <!-- Right Panel: Cart -->
            <section
                class="w-[400px] flex-shrink-0 flex flex-col bg-white rounded-2xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] ring-1 ring-black/5 overflow-hidden relative">

                <div class="p-5 border-b border-slate-100 bg-white">
                    <div class="flex justify-between items-start">
                        <div>
                            @php $orderId = rand(1000, 9999); @endphp
                            <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                                Order #{{ $orderId }}
                            </h2>
                        </div>
                        <div class="flex items-center gap-2">
                            <button onclick="posApp.clearCart()"
                                class="p-2 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-all duration-200"
                                title="Clear All Items">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Cart Items -->
                <div id="cart-items" class="flex-grow overflow-y-auto p-2 custom-scrollbar space-y-1">
                    <!-- Javascript will populate this -->
                </div>

                <!-- Footer / Totals -->
                <div class="mt-auto bg-white border-t border-slate-100 p-5 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] z-20">
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between text-slate-500 text-sm">
                            <span>Subtotal</span>
                            <span id="cart-subtotal"
                                class="font-medium text-slate-700">{{ $outletSettings['currency_symbol'] ?? '$' }}0.00</span>
                        </div>
                        <div class="flex justify-between text-slate-500 text-sm">
                            <span>Service Tax ({{ $outletSettings['tax_rate'] ?? 0 }}%)</span>
                            <span id="cart-tax"
                                class="font-medium text-slate-700">{{ $outletSettings['currency_symbol'] ?? '$' }}0.00</span>
                        </div>
                        <div class="flex justify-between items-end">
                            <span class="text-slate-800 font-bold text-lg">Total</span>
                            <span id="cart-total"
                                class="text-2xl font-extrabold text-indigo-600">{{ $outletSettings['currency_symbol'] ?? '$' }}0.00</span>
                        </div>
                    </div>

                    <button type="button" onclick="posApp.redirectToCheckout()"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 px-6 rounded-xl shadow-lg shadow-indigo-200 transform hover:-translate-y-0.5 hover:shadow-indigo-300 transition-all duration-200 flex justify-between items-center group">
                        <span>Checkout</span>
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5 transform group-hover:translate-x-1 transition-transform" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                </div>
            </section>

        </main>
    </div>

    @push('scripts')
        <script>
            const posApp = {
                apiToken: null,
                products: [],
                cart: [],
                cart: [],
                currency: '{{ $outletSettings['currency_symbol'] ?? '$' }}',
                taxRate: {{ $outletSettings['tax_rate'] ?? 0 }},

                formatPrice(amount) {
                    return this.currency + parseFloat(amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                },

                init() {
                    this.apiToken = this.getApiToken();
                    this.productSearchInput = document.getElementById('product-search-input');
                    this.fetchProducts();
                    this.setupEventListeners();
                    this.updateDateTime();
                    setInterval(() => this.updateDateTime(), 1000);
                },

                getApiToken() {
                    return '{{ $apiToken }}';
                },

                updateDateTime() {
                    const now = new Date();
                    const options = {
                        weekday: 'short', year: 'numeric', month: 'short', day: 'numeric',
                        hour: '2-digit', minute: '2-digit', second: '2-digit'
                    };
                    const el = document.getElementById('current-date-time');
                    if (el) el.innerText = now.toLocaleDateString('en-US', options);
                },

                fetchProducts() {
                    const query = this.productSearchInput ? this.productSearchInput.value : '';
                    fetch('{{ route('api.pos.products') }}?query=' + query, {
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
                    if (!productList) return;

                    productList.innerHTML = '';

                    if (this.products.length === 0) {
                        productList.innerHTML = `
                                                                                                                                                                        <div class="col-span-full flex flex-col items-center justify-center text-slate-400 py-20">
                                                                                                                                                                            <svg class="w-16 h-16 mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                                                                                                                                                            <p class="text-lg font-medium">No products found</p>
                                                                                                                                                                            <p class="text-sm">Try searching for something else</p>
                                                                                                                                                                        </div>
                                                                                                                                                                    `;
                        return;
                    }

                    this.products.forEach(product => {

                        // Image Fallback Logic
                        const imageUrl = product.image || product.image_url; // Adjust key based on actual API
                        let imageHtml = '';

                        if (imageUrl) {
                            imageHtml = `
                                                                                    <img src="${imageUrl}" 
                                                                                         alt="${product.name}" 
                                                                                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                                                                                         onerror="this.onerror=null; this.parentElement.innerHTML=posApp.getFallbackHtml('${product.name.replace(/'/g, "\\'")}')">
                                                                                    `;
                        } else {
                            imageHtml = this.getFallbackHtml(product.name);
                        }

                        const productCard = `
                                                                                    <div class="group bg-white rounded-xl border border-slate-100 shadow-sm hover:shadow-lg hover:border-indigo-100 transition-all duration-300 cursor-pointer overflow-hidden flex flex-col h-full transform hover:-translate-y-1"
                                                                                            data-product-id="${product.id}"
                                                                                            data-product-name="${product.name}"
                                                                                            data-product-price="${product.price}">

                                                                                        <!-- Image Area -->
                                                                                        <div class="aspect-[3/2] bg-slate-50 relative overflow-hidden">
                                                                                            ${imageHtml}

                                                                                            <div class="absolute bottom-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                                                                                <div class="bg-white/90 backdrop-blur-sm p-2 rounded-full shadow-sm text-indigo-600">
                                                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                                                                    </svg>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>

                                                                                        <!-- Content -->
                                                                                        <div class="p-3 flex flex-col flex-grow">
                                                                                            <h3 class="font-bold text-slate-800 text-base leading-snug mb-1 group-hover:text-indigo-600 transition-colors line-clamp-2" title="${product.name}">${product.name}</h3>
                                                                                            <p class="text-xs text-slate-500 line-clamp-2 mb-3 h-8">${product.description || ''}</p>

                                                                                            <div class="mt-auto flex items-center justify-between">
                                                                                                <span class="font-extrabold text-slate-900 text-lg">${this.formatPrice(product.price)}</span>
                                                                                                <button class="add-to-cart-btn bg-slate-100 hover:bg-indigo-600 hover:text-white text-slate-700 p-2 rounded-lg transition-colors duration-200">
                                                                                                    <span class="text-xs font-bold px-1">ADD</span>
                                                                                                </button>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                `;
                        productList.innerHTML += productCard;
                    });
                    this.setupAddToCartButtons();
                },

                // New helper for fallback HTML
                getFallbackHtml(name) {
                    // Generate initials (max 2 chars)
                    const initials = name.split(' ').map(n => n[0]).slice(0, 2).join('').toUpperCase();

                    // Generate consistent pastel color based on name
                    let hash = 0;
                    for (let i = 0; i < name.length; i++) {
                        hash = name.charCodeAt(i) + ((hash << 5) - hash);
                    }
                    const hue = Math.abs(hash % 360);
                    const bgColor = `hsl(${hue}, 70%, 90%)`;
                    const textColor = `hsl(${hue}, 70%, 35%)`;

                    return `
                                                                                    <div class="w-full h-full flex items-center justify-center transition-transform duration-500 group-hover:scale-110" style="background-color: ${bgColor}; color: ${textColor};">
                                                                                        <span class="text-3xl font-extrabold tracking-wider select-none">${initials}</span>
                                                                                    </div>
                                                                                `;
                }, // End getFallbackHtml, and continuation of object...

                setupAddToCartButtons() { // Re-declaring to ensure valid object syntax structure in replace

                },

                setupEventListeners() {
                    const searchInput = document.getElementById('product-search-input');
                    if (searchInput) {
                        searchInput.addEventListener('input', () => this.fetchProducts());
                        searchInput.focus(); // Auto focus on load
                    }
                },

                redirectToCheckout() {
                    if (this.cart.length === 0) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Cart is Empty',
                            text: 'Please add items before checking out.',
                            buttonsStyling: false,
                            confirmButtonText: 'OK',
                            customClass: {
                                popup: 'rounded-3xl shadow-xl',
                                confirmButton: 'bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-indigo-200 transition-transform transform hover:scale-105'
                            }
                        });
                        return;
                    }

                    // Calculate totals for the invoice
                    let subtotal = 0;

                    // Header - Clean & Minimalist
                    let invoiceHtml = `
                                                        <div class="text-left w-full">
                                                            <div class="flex justify-between items-end mb-6 pb-4 border-b border-dashed border-slate-200">
                                                                <div>
                                                                    <h3 class="text-slate-900 font-bold text-2xl">Order Summary</h3>
                                                                    <p class="text-slate-500 text-sm mt-1">Order #${Math.floor(1000 + Math.random() * 9000)} • ${new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })}</p>
                                                                </div>
                                                                <div class="text-right">
                                                                    <span class="block text-3xl font-bold text-indigo-600">${this.cart.reduce((acc, item) => acc + item.quantity, 0)}</span>
                                                                    <span class="text-xs text-slate-400 font-medium uppercase tracking-wider">Items</span>
                                                                </div>
                                                            </div>

                                                            <div class="max-h-[350px] overflow-y-auto custom-scrollbar pr-4 -mr-2 mb-8 space-y-4">
                                                    `;

                    this.cart.forEach(item => {
                        const itemTotal = item.price * item.quantity;
                        subtotal += itemTotal;
                        invoiceHtml += `
                                                            <div class="flex justify-between items-center group">
                                                                <div class="flex items-center gap-4 overflow-hidden">
                                                                     <div class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-center text-sm font-bold text-slate-600">${item.quantity}×</div>
                                                                     <div class="min-w-0">
                                                                        <p class="font-bold text-slate-800 text-base truncate">${item.name}</p>
                                                                        <p class="text-xs text-slate-400 font-medium">@ ${this.formatPrice(item.price)}</p>
                                                                     </div>
                                                                </div>
                                                                <span class="font-bold text-slate-800 text-base whitespace-nowrap pl-4">${this.formatPrice(itemTotal)}</span>
                                                            </div>
                                                        `;
                    });

                    const taxAmount = subtotal * (this.taxRate / 100);
                    const total = subtotal + taxAmount;

                    invoiceHtml += `
                                                            </div>

                                                            <!-- Footer matching Header style -->
                                                            <div class="pt-6 border-t border-dashed border-slate-200 space-y-3">
                                                                <div class="flex justify-between text-base text-slate-500">
                                                                    <span>Subtotal</span>
                                                                    <span class="font-semibold text-slate-700">${this.formatPrice(subtotal)}</span>
                                                                </div>
                                                                <div class="flex justify-between text-base text-slate-500 pb-4 border-b border-slate-100">
                                                                    <span>Service Tax (${this.taxRate}%)</span>
                                                                    <span class="font-semibold text-slate-700">${this.formatPrice(taxAmount)}</span>
                                                                </div>
                                                                <div class="flex justify-between items-center pt-2">
                                                                    <span class="text-slate-800 font-bold text-xl">Total Amount</span>
                                                                    <span class="text-3xl font-black text-indigo-600">${this.formatPrice(total)}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    `;

                    Swal.fire({
                        html: invoiceHtml,
                        showCancelButton: true,
                        buttonsStyling: false,
                        confirmButtonText: 'Confirm & Pay',
                        cancelButtonText: 'Cancel',
                        width: 600,
                        padding: '2.5rem',
                        customClass: {
                            popup: 'rounded-[24px] shadow-2xl',
                            actions: 'gap-2',
                            confirmButton: 'bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold py-4 px-8 text-base shadow-lg shadow-indigo-200 transition-all transform hover:scale-105',
                            cancelButton: 'bg-white hover:bg-slate-50 text-slate-500 border border-slate-200 rounded-xl font-bold py-4 px-8 text-base transition-colors'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Persist cart for checkout page
                            localStorage.setItem('pos_cart', JSON.stringify(this.cart));
                            window.location.href = '{{ route('pos.checkout') }}';
                        }
                    });
                },

                setupAddToCartButtons() {
                    document.querySelectorAll('.add-to-cart-btn, [data-product-id]').forEach(el => {
                        // Prevent double binding if card click logic is needed
                        // Here we assume clicking anywhere on the card OR the button adds it
                        // But let's keep it simple: Card click adds item
                        // If element is the wrapper
                        if (el.hasAttribute('data-product-id')) {
                            el.addEventListener('click', (e) => {
                                const productId = el.dataset.productId;
                                const productName = el.dataset.productName;
                                const productPrice = parseFloat(el.dataset.productPrice);
                                this.addToCart(productId, productName, productPrice);

                                // Visual feedback
                                const originalTransform = el.style.transform;
                                el.style.transform = 'scale(0.98)';
                                setTimeout(() => el.style.transform = '', 100);
                            });
                        }
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

                    // Toast notification
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top',
                        showConfirmButton: false,
                        timer: 1500,
                        timerProgressBar: false,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        },
                        customClass: { popup: 'rounded-xl mt-4 shadow-lg' }
                    })

                    Toast.fire({
                        icon: 'success',
                        title: `Added ${productName}`
                    })

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
                    if (!cartItemsContainer) return;

                    cartItemsContainer.innerHTML = '';

                    if (this.cart.length === 0) {
                        cartItemsContainer.innerHTML = `
                                                                                    <div class="h-full flex flex-col items-center justify-center text-slate-400 p-8 text-center opacity-60">
                                                                                        <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                                                                        <p class="text-sm">Your cart is currently empty.</p>
                                                                                    </div>
                                                                                `;
                        // Reset totals
                        document.getElementById('cart-subtotal').innerText = this.formatPrice(0);
                        document.getElementById('cart-tax').innerText = this.formatPrice(0);
                        document.getElementById('cart-total').innerText = this.formatPrice(0);
                        return;
                    }

                    let subtotal = 0;
                    this.cart.slice().reverse().forEach((item, index) => { // Show newest on top? or bottom. Array order is usually append. Let's keep normal order for now.
                        const itemTotal = item.price * item.quantity;
                        subtotal += itemTotal;

                        const cartItem = `
                                                                                        <div class="group flex items-center justify-between p-3 mb-3 bg-white rounded-xl border border-slate-100 shadow-sm hover:border-indigo-200 transition-all animate-fade-in" style="animation-duration: 0.3s">

                                                                                            <!-- Info & Qty -->
                                                                                            <div class="flex-grow min-w-0 pr-3">
                                                                                                <div class="flex justify-between items-start mb-1.5">
                                                                                                    <p class="font-bold text-slate-800 text-sm truncate leading-tight w-full" title="${item.name}">${item.name}</p>
                                                                                                </div>

                                                                                                <div class="flex items-center justify-between">
                                                                                                    <!-- Qty Controls -->
                                                                                                    <div class="flex items-center bg-slate-50 rounded-lg border border-slate-200 p-0.5">
                                                                                                        <button data-product-id="${item.id}" class="remove-from-cart-btn w-6 h-6 flex items-center justify-center text-slate-500 hover:bg-white hover:text-red-500 hover:shadow-sm rounded-md transition-all">
                                                                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                                                                                            </svg>
                                                                                                        </button>
                                                                                                        <span class="font-mono font-bold text-slate-700 text-sm w-8 text-center select-none">${item.quantity}</span>
                                                                                                        <button onclick="posApp.addToCart(${item.id}, '${item.name.replace(/'/g, "\\'")}', ${item.price})" 
                                                                                                            class="w-6 h-6 flex items-center justify-center text-slate-500 hover:bg-white hover:text-green-600 hover:shadow-sm rounded-md transition-all">
                                                                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                                                                            </svg>
                                                                                                        </button>
                                                                                                    </div>

                                                                                                    <div class="text-right">
                                                                                                        <span class="font-bold text-slate-900 text-sm block">${this.formatPrice(itemTotal)}</span>
                                                                                                        <span class="text-[10px] text-slate-400 font-medium block">@ ${this.formatPrice(item.price)}/ea</span>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    `;
                        cartItemsContainer.innerHTML += cartItem;
                    });

                    const taxAmount = subtotal * (this.taxRate / 100);
                    const total = subtotal + taxAmount;

                    document.getElementById('cart-subtotal').innerText = this.formatPrice(subtotal);
                    document.getElementById('cart-tax').innerText = this.formatPrice(taxAmount);
                    document.getElementById('cart-total').innerText = this.formatPrice(total);

                    this.setupRemoveFromCartButtons();
                },

                setupRemoveFromCartButtons() {
                    document.querySelectorAll('.remove-from-cart-btn').forEach(button => {
                        button.addEventListener('click', (event) => {
                            event.stopPropagation(); // Stop card click if tailored that way
                            const btn = event.target.closest('button');
                            const productId = btn.dataset.productId;
                            this.removeItemFromCart(productId);
                        });
                    });
                },

                clearCart() {
                    if (this.cart.length === 0) {
                        Swal.fire({
                            icon: 'info',
                            title: 'Cart is empty',
                            timer: 1500,
                            showConfirmButton: false,
                            customClass: { popup: 'rounded-3xl shadow-xl' }
                        });
                        return;
                    }

                    Swal.fire({
                        title: 'Clear Cart?',
                        text: "Are you sure you want to remove all items?",
                        icon: 'warning',
                        showCancelButton: true,
                        buttonsStyling: false,
                        confirmButtonText: 'Yes, clear it',
                        cancelButtonText: 'Cancel',
                        customClass: {
                            popup: 'rounded-3xl shadow-xl',
                            actions: 'gap-2',
                            confirmButton: 'bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg shadow-red-200 transition-transform transform hover:scale-105',
                            cancelButton: 'bg-white hover:bg-slate-50 text-slate-500 border border-slate-200 font-bold py-3 px-6 rounded-xl transition-colors'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.cart = [];
                            this.renderCart();

                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top',
                                showConfirmButton: false,
                                timer: 1500,
                                customClass: { popup: 'rounded-xl mt-4 shadow-lg' }
                            });
                            Toast.fire({ icon: 'success', title: 'Cart cleared' });
                        }
                    });
                }
            };

            document.addEventListener('DOMContentLoaded', () => {
                posApp.init();
            });
        </script>
    @endpush
@endsection