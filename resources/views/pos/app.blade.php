@extends('layouts.app')

@section('content')
    @php $theme = $outletSettings['pos_theme_color'] ?? 'indigo'; @endphp
    <div x-data="posApp" class="flex flex-col h-screen bg-slate-100 font-sans antialiased text-slate-800 overflow-hidden">

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
                        class="w-8 h-8 bg-{{ $theme }}-600 rounded-lg flex items-center justify-center text-white font-bold text-lg shadow-{{ $theme }}-200 shadow-md">
                        P
                    </div>
                    <h1 class="text-xl font-bold tracking-tight text-slate-900">POS Terminal</h1>
                </div>
                <div class="h-8 w-px bg-slate-200 mx-2"></div>

                <!-- Date & Time -->
                <span id="current-date-time" class="text-sm font-bold text-slate-600 font-mono tracking-wide mr-4"></span>

                <!-- Globe Status Toggle : Moved to Right -->

            </div>

            {{-- User Profile & Actions --}}
            {{-- Actions --}}
            <div class="flex items-center gap-4">
                <!-- Globe Status Toggle -->
                <button @click="toggleOfflineMode()"
                    class="flex items-center gap-2 px-3 py-1.5 rounded-lg border transition-all duration-300 group hover:shadow-sm"
                    :class="isOnline && !forcedOffline ? 'bg-green-50 border-green-200 hover:bg-green-100' : 'bg-red-50 border-red-200 hover:bg-red-100'"
                    title="Click to toggle status">

                    <!-- Globe Icon -->
                    <div class="relative">
                        <!-- Online Globe -->
                        <svg x-show="isOnline && !forcedOffline" xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                        </svg>
                        <!-- Offline Globe (Crossed) -->
                        <svg x-show="!(isOnline && !forcedOffline)" xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                            <line x1="3" y1="3" x2="21" y2="21" class="text-red-600" stroke-width="3" />
                        </svg>
                    </div>

                    <!-- Text Label -->
                    <span class="font-bold text-sm tracking-wide"
                        :class="isOnline && !forcedOffline ? 'text-green-700' : 'text-red-700'"
                        x-text="(isOnline && !forcedOffline) ? 'Online' : 'Offline'"></span>
                </button>

                <div class="h-8 w-px bg-slate-200"></div>

                <button onclick="posApp.openHistory()"
                    class="flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-lg text-slate-600 hover:bg-slate-50 hover:text-{{ $theme }}-600 hover:border-{{ $theme }}-200 transition-all shadow-sm group">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:scale-110 transition-transform"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-medium text-sm">History</span>
                </button>

                <div class="h-8 w-px bg-slate-200"></div>

                <div class="flex items-center gap-3 px-3 py-1.5 bg-slate-50 rounded-full border border-slate-200">
                    <div
                        class="w-8 h-8 rounded-full bg-{{ $theme }}-100 text-{{ $theme }}-600 flex items-center justify-center font-bold text-sm">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div class="flex flex-col pr-2">
                        <span class="text-xs font-bold text-slate-700 leading-tight">{{ Auth::user()->name }}</span>
                        <span class="text-[10px] font-medium text-slate-500 uppercase tracking-wider leading-tight">
                            {{ Auth::user()->outlet->name ?? 'No Outlet' }}
                        </span>
                    </div>
                </div>

                <div class="h-8 w-px bg-slate-200"></div>

                <button @click="lockScreen()"
                    class="group flex items-center justify-center w-10 h-10 rounded-full bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition-all duration-200 shadow-sm"
                    title="Lock Screen">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </button>

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
                            <svg class="h-5 w-5 text-{{ $theme }}-500" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" id="product-search-input"
                            class="block w-full pl-12 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl leading-5 placeholder-slate-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-{{ $theme }}-500/20 focus:border-{{ $theme }}-500 transition-all duration-200 sm:text-sm"
                            placeholder="Search by product name, SKU, or barcode...">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <span class="text-xs text-slate-400 border border-slate-200 rounded px-1.5 py-0.5">⌘K</span>
                        </div>
                    </div>
                </div>

                <!-- Category Tabs -->
                <div class="px-5 py-2 overflow-x-auto custom-scrollbar">
                    <div id="category-tabs" class="flex gap-3 pb-2 min-w-max">
                        <!-- JS populated -->
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
                            <div id="cart-customer-container" class="flex items-center gap-2 mt-1">
                                <p id="cart-customer-name"
                                    class="text-xs text-slate-400 font-medium cursor-pointer hover:text-{{ $theme }}-600 transition-colors"
                                    onclick="posApp.openCustomerModal()">
                                    Guest Customer
                                </p>
                                <button id="remove-customer-btn" onclick="posApp.removeCustomer()"
                                    class="hidden text-slate-400 hover:text-red-500 transition-colors"
                                    title="Remove Customer">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button onclick="posApp.openCustomerModal()"
                                class="p-2 rounded-lg text-slate-400 hover:text-{{ $theme }}-600 hover:bg-slate-50 transition-all duration-200"
                                title="Add Customer">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </button>
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
                                class="text-2xl font-extrabold text-{{ $theme }}-600">{{ $outletSettings['currency_symbol'] ?? '$' }}0.00</span>
                        </div>
                    </div>

                    <button type="button" onclick="posApp.redirectToCheckout()"
                        class="w-full bg-{{ $theme }}-600 hover:bg-{{ $theme }}-700 text-white font-bold py-4 px-6 rounded-xl shadow-lg shadow-{{ $theme }}-200 transform hover:-translate-y-0.5 hover:shadow-{{ $theme }}-300 transition-all duration-200 flex justify-between items-center group">
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

    <div id="history-modal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" onclick="posApp.closeHistory()">
        </div>

        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-4xl border border-slate-100">

                    <!-- Modal Header -->
                    <div class="bg-white px-6 py-5 border-b border-slate-100 flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="bg-{{ $theme }}-100 p-2 rounded-lg text-{{ $theme }}-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-slate-900" id="modal-title">Transaction History
                                </h3>
                                <p class="text-sm text-slate-500">View and manage recent sales</p>
                            </div>
                        </div>
                        <button onclick="posApp.closeHistory()"
                            class="text-slate-400 hover:text-slate-600 transition-colors bg-slate-50 hover:bg-slate-100 p-2 rounded-lg">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="bg-slate-50/50 px-6 py-6 h-[500px] flex flex-col">
                        <!-- Search Bar -->
                        <div class="mb-4">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input type="text" id="history-search"
                                    class="block w-full pl-10 pr-3 py-2.5 border border-slate-200 rounded-xl leading-5 bg-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-{{ $theme }}-200 focus:border-{{ $theme }}-500 sm:text-sm transition-all"
                                    placeholder="Search by Order ID...">
                            </div>
                        </div>

                        <!-- Table -->
                        <div class="flex-grow overflow-hidden rounded-xl border border-slate-200 bg-white flex flex-col">
                            <div class="overflow-y-auto custom-scrollbar flex-grow">
                                <table class="min-w-full divide-y divide-slate-100">
                                    <thead class="bg-slate-50 sticky top-0 z-10">
                                        <tr>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                                Order ID</th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                                Time</th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                                Total</th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                                Status</th>
                                            <th scope="col"
                                                class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">
                                                Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="history-list-body" class="bg-white divide-y divide-slate-100">
                                        <!-- Populated via JS -->
                                    </tbody>
                                </table>
                            </div>
                            <!-- Pagination -->
                            <div class="bg-slate-50 px-6 py-3 border-t border-slate-100 flex items-center justify-between">
                                <span class="text-xs text-slate-500">Showing recent transactions</span>
                                <div class="flex gap-2">
                                    <button id="prev-page-btn"
                                        class="px-3 py-1 bg-white border border-slate-200 rounded text-xs font-medium text-slate-600 hover:bg-slate-100 disabled:opacity-50">Previous</button>
                                    <button id="next-page-btn"
                                        class="px-3 py-1 bg-white border border-slate-200 rounded text-xs font-medium text-slate-600 hover:bg-slate-100 disabled:opacity-50">Next</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modifier Modal -->
    <div id="modifier-modal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"></div>

        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-100">
                    <div class="bg-white px-6 py-5 border-b border-slate-100">
                        <h3 class="text-xl font-bold text-slate-900" id="modal-title">Customize Item</h3>
                        <p class="text-sm text-slate-500">Select modifiers for this product</p>
                    </div>

                    <div class="p-6 max-h-[60vh] overflow-y-auto custom-scrollbar" id="modifier-options-container">
                        <!-- Modifiers injected here -->
                    </div>

                    <div class="bg-slate-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-slate-100">
                        <button type="button" onclick="posApp.confirmModifiers()"
                            class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-3 bg-{{ $theme }}-600 text-base font-bold text-white hover:bg-{{ $theme }}-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-{{ $theme }}-500 sm:w-auto sm:text-sm shadow-md shadow-{{ $theme }}-200">
                            Add to Order
                        </button>
                        <button type="button" onclick="posApp.closeModifierModal()"
                            class="mt-3 w-full inline-flex justify-center rounded-xl border border-slate-300 shadow-sm px-4 py-3 bg-white text-base font-bold text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Modal -->
    <div id="customer-modal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"
            onclick="posApp.closeCustomerModal()"></div>

        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-100">

                    <!-- Modal Header -->
                    <div class="bg-white px-6 py-5 border-b border-slate-100 flex justify-between items-center">
                        <h3 class="text-xl font-bold text-slate-900">Select Customer</h3>
                        <button onclick="posApp.closeCustomerModal()"
                            class="text-slate-400 hover:text-slate-600 transition-colors">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Tabs -->
                    <div class="flex border-b border-slate-200">
                        <button onclick="posApp.switchCustomerTab('search')" id="tab-search-btn"
                            class="flex-1 py-3 text-sm font-bold text-{{ $theme }}-600 border-b-2 border-{{ $theme }}-600 bg-slate-50">Search</button>
                        <button onclick="posApp.switchCustomerTab('create')" id="tab-create-btn"
                            class="flex-1 py-3 text-sm font-medium text-slate-500 hover:text-slate-700">Register
                            New</button>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-6">
                        <!-- Search Tab -->
                        <div id="customer-tab-search">
                            <div class="relative mb-4">
                                <input type="text" id="customer-search-input"
                                    class="block w-full pl-4 pr-10 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-{{ $theme }}-500 focus:border-{{ $theme }}-500 transition-all"
                                    placeholder="Search name or phone..." autofocus>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div id="customer-search-results"
                                class="max-h-[300px] overflow-y-auto custom-scrollbar space-y-2">
                                <!-- Results -->
                                <p class="text-center text-slate-400 py-4 text-sm">Start typing to search...</p>
                            </div>
                        </div>

                        <!-- Create Tab -->
                        <div id="customer-tab-create" class="hidden space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Full Name</label>
                                <input type="text" id="new-customer-name"
                                    class="block w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-{{ $theme }}-500 focus:border-{{ $theme }}-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Phone Number</label>
                                <input type="tel" id="new-customer-phone"
                                    class="block w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-{{ $theme }}-500 focus:border-{{ $theme }}-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Email
                                    (Optional)</label>
                                <input type="email" id="new-customer-email"
                                    class="block w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-{{ $theme }}-500 focus:border-{{ $theme }}-500">
                            </div>
                            <button onclick="posApp.createCustomer()"
                                class="w-full bg-{{ $theme }}-600 hover:bg-{{ $theme }}-700 text-white font-bold py-3 rounded-xl shadow-md shadow-{{ $theme }}-200 transition-all">
                                Create & Select Customer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const posApp = {
                apiToken: null,
                categories: [],
                activeCategoryId: null,
                products: [],
                cart: JSON.parse(localStorage.getItem('pos_cart') || '[]'),
                currency: '{{ $outletSettings['currency_symbol'] ?? '$' }}',
                taxRate: {{ $outletSettings['tax_rate'] ?? 0 }},

                formatPrice(amount) {
                    return this.currency + parseFloat(amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                },

                isOnline: navigator.onLine,
                forcedOffline: localStorage.getItem('pos_forced_offline') === 'true',

                toggleOfflineMode() {
                    this.forcedOffline = !this.forcedOffline;
                    localStorage.setItem('pos_forced_offline', this.forcedOffline);

                    if (this.forcedOffline) {
                        Swal.fire({
                            toast: true, position: 'top', icon: 'info',
                            title: 'Offline Mode Enabled', text: 'Transactions will be queued locally.',
                            timer: 2000, showConfirmButton: false
                        });
                    } else {
                        // When switching back to online, check real connection
                        if (navigator.onLine) {
                            this.syncOfflineSales();
                            Swal.fire({
                                toast: true, position: 'top', icon: 'success',
                                title: 'Back Online', timer: 2000, showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                toast: true, position: 'top', icon: 'warning',
                                title: 'No Internet', text: 'Still offline due to network.',
                                timer: 2000, showConfirmButton: false
                            });
                        }
                    }
                },

                init() {
                    this.apiToken = this.getApiToken();
                    this.productSearchInput = document.getElementById('product-search-input');

                    // Initial Sync check if starting online and not forced
                    if (this.isOnline && !this.forcedOffline) {
                        setTimeout(() => this.syncOfflineSales(), 1000);
                    }

                    // Network Listeners
                    window.addEventListener('online', () => {
                        this.isOnline = true;
                        if (!this.forcedOffline) {
                            this.syncOfflineSales();
                            Swal.fire({
                                toast: true, position: 'top', icon: 'success',
                                title: 'Back Online', timer: 2000, showConfirmButton: false,
                                customClass: { popup: 'rounded-xl mt-4 shadow-lg' }
                            });
                        }
                    });
                    window.addEventListener('offline', () => {
                        this.isOnline = false;
                        Swal.fire({
                            toast: true, position: 'top', icon: 'warning',
                            title: 'You are Offline', text: 'Sales will be saved locally.',
                            timer: 3000, showConfirmButton: false,
                            customClass: { popup: 'rounded-xl mt-4 shadow-lg' }
                        });
                    });

                    this.fetchCategories();
                    this.fetchProducts();
                    this.setupEventListeners();
                    this.updateDateTime();
                    setInterval(() => this.updateDateTime(), 1000);

                    // Idle Timer Init
                    ['mousemove', 'mousedown', 'keypress', 'touchmove'].forEach(evt => {
                        document.addEventListener(evt, () => this.resetIdleTimer());
                    });
                    this.resetIdleTimer();

                    // Load Customer
                    const storedCustomer = localStorage.getItem('pos_customer');
                    if (storedCustomer) {
                        this.cartCustomer = JSON.parse(storedCustomer);
                        const nameDisplay = document.getElementById('cart-customer-name');
                        const removeBtn = document.getElementById('remove-customer-btn');
                        if (nameDisplay) {
                            nameDisplay.innerText = this.cartCustomer.name;
                            nameDisplay.classList.add('text-{{ $theme }}-600', 'font-bold');
                        }
                        if (removeBtn) removeBtn.classList.remove('hidden');
                    }

                    // Render initial cart if loaded from storage
                    if (this.cart.length > 0) {
                        this.renderCart();
                    }

                    // Keyboard Shortcuts
                    document.addEventListener('keydown', (e) => {
                        // CMD/CTRL + K: Focus Search
                        if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
                            e.preventDefault();
                            if (this.productSearchInput) this.productSearchInput.focus();
                        }

                        // ESC: Close Modals or Clear Search
                        if (e.key === 'Escape') {
                            if (!document.getElementById('modifier-modal').classList.contains('hidden')) {
                                this.closeModifierModal();
                            } else if (!document.getElementById('history-modal').classList.contains('hidden')) {
                                this.closeHistory();
                            } else if (!document.getElementById('customer-modal').classList.contains('hidden')) {
                                this.closeCustomerModal();
                            } else if (document.activeElement === this.productSearchInput) {
                                this.productSearchInput.blur();
                            }
                        }

                        // CMD/CTRL + S: Checkout
                        if ((e.metaKey || e.ctrlKey) && e.key === 's') {
                            e.preventDefault();
                            this.redirectToCheckout();
                        }

                        // CMD/CTRL + H: History
                        if ((e.metaKey || e.ctrlKey) && e.key === 'h') {
                            e.preventDefault();
                            this.openHistory();
                        }
                    });

                },

                // --- Modifier Logic ---
                currentProductForModifiers: null,
                selectedModifiers: {}, // { modifier_id: [item_id, ...] }

                openModifierModal(product) {
                    this.currentProductForModifiers = product;
                    this.selectedModifiers = {};

                    // Render Modal Content
                    const container = document.getElementById('modifier-options-container');
                    container.innerHTML = '';

                    product.modifiers.forEach(mod => {
                        let optionsHtml = '';
                        mod.items.forEach(item => {
                            const inputType = mod.type === 'multiple' ? 'checkbox' : 'radio';
                            const inputName = `modifier_${mod.id}`;
                            const checked = false;

                            optionsHtml += `
                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <label class="flex items-center justify-between p-3 border rounded-lg cursor-pointer hover:bg-slate-50 transition-colors">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <div class="flex items-center">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <input type="${inputType}" name="${inputName}" value="${item.id}" 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    data-modifier-id="${mod.id}"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    data-item-price="${item.price}"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    data-item-name="${item.name}"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    class="w-4 h-4 text-{{ $theme }}-600 border-gray-300 focus:ring-{{ $theme }}-500"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    onchange="posApp.handleModifierChange(this, '${mod.type}')">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <span class="ml-3 font-medium text-slate-700">${item.name}</span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <span class="text-sm text-slate-500">+${this.formatPrice(item.price)}</span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </label>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                    `;
                        });

                        const html = `
                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div class="mb-4">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <h4 class="font-bold text-slate-800 mb-2">${mod.name} <span class="text-xs font-normal text-slate-500">(${mod.type === 'multiple' ? 'Choose multiple' : 'Choose one'})</span></h4>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <div class="space-y-2">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                            ${optionsHtml}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                `;
                        container.innerHTML += html;
                    });

                    document.getElementById('modifier-modal').classList.remove('hidden');
                },

                handleModifierChange(input, type) {
                    const modId = input.dataset.modifierId;
                    const itemId = parseInt(input.value);
                    const itemPrice = parseFloat(input.dataset.itemPrice);
                    const itemName = input.dataset.itemName;

                    if (type === 'single') {
                        // Reset this modifier group
                        this.selectedModifiers[modId] = [{ id: itemId, price: itemPrice, name: itemName }];
                    } else {
                        if (!this.selectedModifiers[modId]) this.selectedModifiers[modId] = [];

                        if (input.checked) {
                            this.selectedModifiers[modId].push({ id: itemId, price: itemPrice, name: itemName });
                        } else {
                            this.selectedModifiers[modId] = this.selectedModifiers[modId].filter(i => i.id !== itemId);
                        }
                    }
                },

                closeModifierModal() {
                    document.getElementById('modifier-modal').classList.add('hidden');
                    this.currentProductForModifiers = null;
                    this.selectedModifiers = {};
                },

                confirmModifiers() {
                    // Flatten selected modifiers
                    let flatModifiers = [];
                    Object.values(this.selectedModifiers).forEach(items => {
                        flatModifiers = flatModifiers.concat(items);
                    });

                    this.addItemToCart(this.currentProductForModifiers, flatModifiers);
                    this.closeModifierModal();
                },

                // --- End Modifier Logic ---




                syncOfflineSales() {
                    const offlineSales = JSON.parse(localStorage.getItem('pos_offline_sales') || '[]');
                    if (offlineSales.length === 0) return;

                    const total = offlineSales.length;
                    let synced = 0;

                    const syncNext = (index) => {
                        if (index >= offlineSales.length) {
                            // Clean up processed
                            if (synced === total) {
                                localStorage.removeItem('pos_offline_sales');
                                Swal.fire({
                                    toast: true, position: 'top', icon: 'success',
                                    title: 'Sync Complete', text: `${synced} sales uploaded.`,
                                    timer: 3000, showConfirmButton: false,
                                    customClass: { popup: 'rounded-xl mt-4 shadow-lg' }
                                });
                            } else {
                                // Filter out synced ones based on some criteria? 
                                // Since we don't modify the array in place, we can't easily know which ones failed if we don't track.
                                // Simplification: If any fail, we keep all remaining from that index? 
                                // Ideally we should mark them or remove one by one. 
                                // Let's remove them one by one as they succeed.
                                return;
                            }
                            return;
                        }

                        const sale = offlineSales[index];
                        // If sale has temp ID, backend should handle it (ignore or use). 
                        // To be safe, let's remove 'id' if it starts with 'OFF-'.
                        let payload = { ...sale };
                        if (payload.id && String(payload.id).startsWith('OFF-')) {
                            delete payload.id;
                        }
                        delete payload.is_offline; // unwanted field

                        fetch('{{ route('api.pos.sales') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Authorization': 'Bearer ' + this.apiToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(payload)
                        })
                            .then(res => {
                                if (res.ok) {
                                    synced++;
                                    // Remove this specific item from local storage to avoid duplicates if crash
                                    // Reading fresh locally just in case? No, let's just use memory array and update storage at end or step.
                                    // Better: Update storage after each success.
                                    const current = JSON.parse(localStorage.getItem('pos_offline_sales') || '[]');
                                    const updated = current.filter(s => s.id !== sale.id);
                                    localStorage.setItem('pos_offline_sales', JSON.stringify(updated));

                                    syncNext(index + 1);
                                } else {
                                    console.error('Sync failed for item', index);
                                    // Continue to next? Or stop? 
                                    // If error is 422 (validation), maybe skip. If 500, stop.
                                    // Let's stop to be safe.
                                }
                            })
                            .catch(err => {
                                console.error('Sync error', err);
                            });
                    };

                    Swal.fire({
                        toast: true, position: 'top', icon: 'info',
                        title: 'Syncing Sales...', text: `Uploading ${total} offline sales.`,
                        timer: 3000, showConfirmButton: false,
                        customClass: { popup: 'rounded-xl mt-4 shadow-lg' }
                    });

                    syncNext(0);
                },


                // --- Lock Screen Logic ---
                pinInput: '',
                idleTimer: null,
                LOCK_TIMEOUT: 10 * 60 * 1000, // 10 Minutes

                resetIdleTimer() {
                    clearTimeout(this.idleTimer);
                    this.idleTimer = setTimeout(() => this.lockScreen(), this.LOCK_TIMEOUT);
                },

                lockScreen() {
                    this.saveCart();
                    window.location.href = '{{ route('pos.lock') }}';
                },

                // --- End Lock Screen Logic ---

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

                fetchCategories() {
                    const tabsContainer = document.getElementById('category-tabs');
                    if (!tabsContainer) return;

                    const renderTabs = (categories) => {
                        tabsContainer.innerHTML = `
                                                                                                                                                                                                                                                                                                                                        <button onclick="posApp.filterCategory('all')" 
                                                                                                                                                                                                                                                                                                                                            class="category-tab px-4 py-2 rounded-xl text-sm font-bold whitespace-nowrap transition-all duration-200 bg-{{ $theme }}-600 text-white shadow-md transform scale-105"
                                                                                                                                                                                                                                                                                                                                            data-category="all">
                                                                                                                                                                                                                                                                                                                                            All Items
                                                                                                                                                                                                                                                                                                                                        </button>
                                                                                                                                                                                                                                                                                                                                    `;
                        categories.forEach(cat => {
                            const btn = document.createElement('button');
                            btn.className = `category-tab px-4 py-2 rounded-xl text-sm font-bold whitespace-nowrap transition-all duration-200 bg-white text-slate-500 hover:bg-slate-50 border border-slate-200`;
                            btn.onclick = () => this.filterCategory(cat.id);
                            btn.innerText = cat.name;
                            btn.dataset.category = cat.id;
                            tabsContainer.appendChild(btn);
                        });
                    };

                    fetch('{{ route('api.pos.categories') }}', {
                        headers: { 'Authorization': `Bearer ${this.apiToken}`, 'Accept': 'application/json' }
                    })
                        .then(res => {
                            if (res.status === 401) {
                                window.location.href = '{{ route('login') }}';
                                return null;
                            }
                            if (!res.ok) throw new Error('Network response was not ok');
                            return res.json();
                        })
                        .then(data => {
                            if (!data) return; // if 401 handled above
                            this.categories = data;
                            localStorage.setItem('pos_categories', JSON.stringify(data));
                            renderTabs(data);
                        })
                        .catch(err => {
                            console.error('Error fetching categories:', err);
                            const cached = localStorage.getItem('pos_categories');
                            if (cached) {
                                this.categories = JSON.parse(cached);
                                renderTabs(this.categories);
                            }
                        });
                },

                filterCategory(id) {
                    this.activeCategoryId = id;
                    document.querySelectorAll('.category-tab').forEach(btn => {
                        if (btn.dataset.category == id || (id === 'all' && btn.dataset.category === 'all')) {
                            btn.classList.add('bg-{{ $theme }}-600', 'text-white', 'shadow-md', 'transform', 'scale-105');
                            btn.classList.remove('bg-white', 'text-slate-500', 'hover:bg-slate-50', 'border', 'border-slate-200');
                        } else {
                            btn.classList.remove('bg-{{ $theme }}-600', 'text-white', 'shadow-md', 'transform', 'scale-105');
                            btn.classList.add('bg-white', 'text-slate-500', 'hover:bg-slate-50', 'border', 'border-slate-200');
                        }
                    });
                    this.fetchProducts();
                },

                fetchProducts() {
                    const productList = document.getElementById('product-list');
                    if (!productList) return;

                    productList.innerHTML = `
                                                                                                                                                                                                                                                                                                                                    <div class="col-span-full flex flex-col items-center justify-center h-64 text-slate-400">
                                                                                                                                                                                                                                                                                                                                         <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-current mb-2"></div>
                                                                                                                                                                                                                                                                                                                                         <p>Loading products...</p>
                                                                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                                                                `;

                    // Handle filter by category if selecting specific category
                    let url = '{{ route('api.pos.products') }}';
                    if (this.activeCategoryId && this.activeCategoryId !== 'all') {
                        url += '?category_id=' + this.activeCategoryId;
                    }

                    if (!this.isOnline) {
                        // Offline Filter Logic
                        const cached = localStorage.getItem('pos_products');
                        if (cached) {
                            let prods = JSON.parse(cached);
                            if (this.activeCategoryId && this.activeCategoryId !== 'all') {
                                prods = prods.filter(p => p.category_id == this.activeCategoryId);
                            }
                            this.products = prods;
                            this.filteredProducts = prods;
                            this.renderProducts();
                            // If user is searching, filter by search too (not implemented here fully inside fetch, usually separate)
                            // But keeping logical consistency with online search would be good. 
                            // For simplicity, offline mode might just load all and let client-side search handle it? 
                            // Current arch loads ALL products then client filters? No, previous code had server query.
                            // If previous code had server query, offline search is harder. 
                            // Suggestion: Offline mode loads ALL products into cache initially, then client filters.
                        } else {
                            productList.innerHTML = `<div class="col-span-full text-center text-slate-400 py-10">Unable to load products. Check connection.</div>`;
                        }
                        return;
                    }

                    fetch(url, {
                        headers: { 'Authorization': `Bearer ${this.apiToken}`, 'Accept': 'application/json' }
                    })
                        .then(res => {
                            if (res.status === 401) {
                                window.location.href = '{{ route('login') }}';
                                return null;
                            }
                            if (!res.ok) throw new Error('Network response was not ok');
                            return res.json();
                        })
                        .then(data => {
                            if (!data) return;
                            // If we are fetching 'all' products (initial load), cache them. 
                            this.products = data;
                            this.filteredProducts = data;
                            if (!this.activeCategoryId || this.activeCategoryId === 'all') {
                                localStorage.setItem('pos_products', JSON.stringify(data));
                            }
                            this.renderProducts();
                        })
                        .catch(err => {
                            console.error('Error fetching products:', err);
                            const cached = localStorage.getItem('pos_products');
                            if (cached) {
                                let prods = JSON.parse(cached);
                                if (this.activeCategoryId && this.activeCategoryId !== 'all') {
                                    prods = prods.filter(p => p.category_id == this.activeCategoryId);
                                }
                                this.products = prods;
                                this.filteredProducts = prods;
                                this.renderProducts();
                                Swal.fire({
                                    toast: true, position: 'top-end', icon: 'info',
                                    title: 'Offline Mode', text: 'Loaded products from cache',
                                    timer: 3000
                                });
                            }
                        });
                },

                renderProducts() {
                    const productList = document.getElementById('product-list');
                    if (!productList) return;

                    productList.innerHTML = '';

                    if (this.products.length === 0) {
                        productList.innerHTML = `
                                                                                                                                                                                                                                                                                                                            < div class="col-span-full flex flex-col items-center justify-center text-slate-400 py-20" >
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <svg class="w-16 h-16 mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <p class="text-lg font-medium">No products found</p>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <p class="text-sm">Try searching for something else</p>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </div >
                                                                                                                                                                                                                                                                                                                            `;
                        return;
                    }

                    this.products.forEach(product => {

                        // Image Fallback Logic
                        const imageUrl = product.image || product.image_url;
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
                                                                                                                                                                                                                                                                <div class="group bg-white rounded-xl border border-slate-100 shadow-sm hover:shadow-lg hover:border-{{ $theme }}-100 transition-all duration-300 cursor-pointer overflow-hidden flex flex-col h-full transform hover:-translate-y-1"
                                                                                                                                                                                                                                                                    data-product-id="${product.id}"
                                                                                                                                                                                                                                                                    data-product-name="${product.name}"
                                                                                                                                                                                                                                                                    data-product-price="${product.price}">

                                                                                                                                                                                                                                                                    <!-- Image Area -->
                                                                                                                                                                                                                                                                    <div class="aspect-[3/2] bg-slate-50 relative overflow-hidden">
                                                                                                                                                                                                                                                                        ${imageHtml}

                                                                                                                                                                                                                                                                        <div class="absolute bottom-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                                                                                                                                                                                                                                                            <div class="bg-white/90 backdrop-blur-sm p-2 rounded-full shadow-sm text-{{ $theme }}-600">
                                                                                                                                                                                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                                                                                                                                                                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                                                                                                                                                                                                                                                </svg>
                                                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                    </div>

                                                                                                                                                                                                                                                                    <!-- Content -->
                                                                                                                                                                                                                                                                    <div class="p-3 flex flex-col flex-grow">
                                                                                                                                                                                                                                                                        <h3 class="font-bold text-slate-800 text-base leading-snug mb-1 group-hover:text-{{ $theme }}-600 transition-colors line-clamp-2" title="${product.name}">${product.name}</h3>
                                                                                                                                                                                                                                                                        <p class="text-xs text-slate-500 line-clamp-2 mb-3 h-8">${product.description || ''}</p>

                                                                                                                                                                                                                                                                        <div class="mt-auto flex items-center justify-between">
                                                                                                                                                                                                                                                                            <span class="font-extrabold text-slate-900 text-lg">${this.formatPrice(product.price)}</span>
                                                                                                                                                                                                                                                                            <button class="add-to-cart-btn bg-slate-100 hover:bg-{{ $theme }}-600 hover:text-white text-slate-700 p-2 rounded-lg transition-colors duration-200">
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
                                confirmButton: 'bg-{{ $theme }}-600 hover:bg-{{ $theme }}-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-{{ $theme }}-200 transition-transform transform hover:scale-105'
                            }
                        });
                        return;
                    }

                    // Calculate totals for the invoice
                    let subtotal = 0;

                    // Header - Clean & Minimalist
                    let invoiceHtml = `
                                                                                                                                                                                                                                                                                                                            < div class="text-left w-full" >
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <div class="flex justify-between items-end mb-6 pb-4 border-b border-dashed border-slate-200">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <h3 class="text-slate-900 font-bold text-2xl">Order Summary</h3>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <p class="text-slate-500 text-sm mt-1">Order #${Math.floor(1000 + Math.random() * 9000)} • ${new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })}</p>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <div class="text-right">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               <div class="text-right">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <span class="block text-3xl font-bold text-{{ $theme }}-600">${this.cart.reduce((acc, item) => acc + item.quantity, 0)}</span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <span class="text-xs text-slate-400 font-medium uppercase tracking-wider">Items</span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </div>                                                </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </div>

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <div class="max-h-[350px] overflow-y-auto custom-scrollbar pr-4 -mr-2 mb-8 space-y-4">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    `;

                    this.cart.forEach(item => {
                        const itemTotal = (item.unitPrice || item.price) * item.quantity; // Use unitPrice which includes modifier costs
                        subtotal += itemTotal;

                        // Modifiers HTML
                        let modifiersHtml = '';
                        if (item.modifiers && item.modifiers.length > 0) {
                            modifiersHtml = '<div class="text-xs text-slate-500 mt-1 space-y-0.5">';
                            item.modifiers.forEach(mod => {
                                modifiersHtml += `<div class="flex justify-between"><span>+ ${mod.name}</span><span>${this.formatPrice(mod.price)}</span></div>`;
                            });
                            modifiersHtml += '</div>';
                        }

                        invoiceHtml += `
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <div class="p-3 mb-2 bg-slate-50 border border-slate-100 rounded-xl flex flex-col group transition-all duration-300 hover:shadow-sm hover:border-{{ $theme }}-200">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <div class="flex justify-between items-start mb-2">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div class="flex-grow">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <h4 class="font-bold text-slate-800 text-sm leading-tight">${item.quantity}× ${item.name}</h4>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <div class="text-xs text-slate-400 font-medium mt-0.5">@ ${this.formatPrice(item.unitPrice || item.price)}</div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        ${modifiersHtml}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div class="text-right">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <span class="font-extrabold text-slate-800 text-sm">${this.formatPrice((item.unitPrice || item.price) * item.quantity)}</span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </div>
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
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <span class="text-3xl font-black text-{{ $theme }}-600">${this.formatPrice(total)}</span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </div>                                                </div>
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
                            confirmButton: 'bg-{{ $theme }}-600 hover:bg-{{ $theme }}-700 text-white rounded-xl font-bold py-4 px-8 text-base shadow-lg shadow-{{ $theme }}-200 transition-all transform hover:scale-105',
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
                                this.addToCart({ id: productId, name: productName, price: productPrice, modifiers: [] });

                                // Visual feedback
                                const originalTransform = el.style.transform;
                                el.style.transform = 'scale(0.98)';
                                setTimeout(() => el.style.transform = '', 100);
                            });
                        }
                    });
                },

                addToCart(product) {
                    // Check if product has modifiers
                    if (product.modifiers && product.modifiers.length > 0) {
                        this.openModifierModal(product);
                    } else {
                        this.addItemToCart(product, []);
                    }
                },

                addItemToCart(product, selectedModifiers = []) {
                    const price = parseFloat(product.price);

                    // Calculate modifier total cost
                    const modifiersCost = selectedModifiers.reduce((sum, mod) => sum + parseFloat(mod.price), 0);
                    const unitPrice = price + modifiersCost;

                    // Create unique ID based on product AND modifiers
                    // Sort modifiers by ID to ensure consistent key
                    const sortedModifiers = selectedModifiers.slice().sort((a, b) => a.id - b.id);
                    const modifierKey = sortedModifiers.map(m => m.id).join('-');
                    const cartItemId = `${product.id}-${modifierKey}`;

                    const existingItem = this.cart.find(item => item.cartItemId === cartItemId);

                    if (existingItem) {
                        existingItem.quantity++;
                    } else {
                        this.cart.push({
                            cartItemId: cartItemId,
                            id: product.id,
                            name: product.name,
                            price: price, // Base price
                            unitPrice: unitPrice, // Price + Modifiers
                            image: product.image, // Ensure image is passed if available
                            quantity: 1,
                            modifiers: sortedModifiers
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
                        title: `Added ${product.name}`
                    })

                    this.saveCart();
                    this.renderCart();
                },

                saveCart() {
                    localStorage.setItem('pos_cart', JSON.stringify(this.cart));
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
                    this.saveCart();
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
                    this.cart.slice().reverse().forEach((item, index) => {
                        const itemTotal = (item.unitPrice || item.price) * item.quantity;
                        subtotal += itemTotal;

                        // Modifiers HTML
                        let modifiersHtml = '';
                        if (item.modifiers && item.modifiers.length > 0) {
                            modifiersHtml = '<div class="text-xs text-slate-500 mt-1 space-y-0.5 border-t border-dashed border-slate-100 pt-1">';
                            item.modifiers.forEach(mod => {
                                modifiersHtml += `<div class="flex justify-between"><span>+ ${mod.name}</span><span>${this.formatPrice(mod.price)}</span></div>`;
                            });
                            modifiersHtml += '</div>';
                        }

                        const cartItem = `
                                                                                                                                                                                                                                                                                                                                                                        <div class="group flex items-center justify-between p-3 mb-3 bg-white rounded-xl border border-slate-100 shadow-sm hover:border-{{ $theme }}-200 transition-all animate-fade-in" style="animation-duration: 0.3s">

                                                                                                                                                                                                                                                                                                                                                                            <!-- Info & Qty -->
                                                                                                                                                                                                                                                                                                                                                                            <div class="flex-grow min-w-0 pr-3">
                                                                                                                                                                                                                                                                                                                                                                                <div class="flex justify-between items-start mb-1.5">
                                                                                                                                                                                                                                                                                                                                                                                    <p class="font-bold text-slate-800 text-sm truncate leading-tight w-full" title="${item.name}">${item.name}</p>
                                                                                                                                                                                                                                                                                                                                                                                </div>

                                                                                                                                                                                                                                                                                                                                                                                ${modifiersHtml}

                                                                                                                                                                                                                                                                                                                                                                                <div class="flex items-center justify-between mt-2">
                                                                                                                                                                                                                                                                                                                                                                                    <!-- Qty Controls -->
                                                                                                                                                                                                                                                                                                                                                                                    <div class="flex items-center bg-slate-50 rounded-lg border border-slate-200 p-0.5">
                                                                                                                                                                                                                                                                                                                                                                                        <button data-product-id="${item.id}" class="remove-from-cart-btn w-6 h-6 flex items-center justify-center text-slate-500 hover:bg-white hover:text-red-500 hover:shadow-sm rounded-md transition-all">
                                                                                                                                                                                                                                                                                                                                                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                                                                                                                                                                                                                                                                                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                                                                                                                                                                                                                                                                                                                                                                            </svg>
                                                                                                                                                                                                                                                                                                                                                                                        </button>
                                                                                                                                                                                                                                                                                                                                                                                        <span class="font-mono font-bold text-slate-700 text-sm w-8 text-center select-none">${item.quantity}</span>
                                                                                                                                                                                                                                                                                                                                                                                        <button onclick="posApp.addToCart({id: ${item.id}, name: '${(item.name || '').replace(/'/g, "\\'")}', price: ${item.price}, modifiers: []})" 
                                                                                                                                                                                                                                                                                                                                                                                            class="w-6 h-6 flex items-center justify-center text-slate-500 hover:bg-white hover:text-green-600 hover:shadow-sm rounded-md transition-all">
                                                                                                                                                                                                                                                                                                                                                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                                                                                                                                                                                                                                                                                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                                                                                                                                                                                                                                                                                                                                                            </svg>
                                                                                                                                                                                                                                                                                                                                                                                        </button>
                                                                                                                                                                                                                                                                                                                                                                                    </div>

                                                                                                                                                                                                                                                                                                                                                                                    <div class="text-right">
                                                                                                                                                                                                                                                                                                                                                                                        <span class="font-bold text-slate-900 text-sm block">${this.formatPrice(itemTotal)}</span>
                                                                                                                                                                                                                                                                                                                                                                                        <span class="text-[10px] text-slate-400 font-medium block">@ ${this.formatPrice(item.unitPrice || item.price)}/ea</span>
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
                            this.saveCart();
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
                },

                // --- History Features ---

                openHistory() {
                    document.getElementById('history-modal').classList.remove('hidden');
                    this.fetchHistory();

                    // Setup search listener if not already
                    const searchInput = document.getElementById('history-search');
                    if (searchInput && !searchInput.dataset.listening) {
                        searchInput.addEventListener('input', (e) => {
                            clearTimeout(this.searchTimeout);
                            this.searchTimeout = setTimeout(() => {
                                this.fetchHistory(1, e.target.value);
                            }, 500);
                        });
                        searchInput.dataset.listening = true;
                    }

                    // Setup pagination
                    document.getElementById('prev-page-btn').onclick = () => {
                        if (this.historyCurrentPage > 1) this.fetchHistory(this.historyCurrentPage - 1);
                    };
                    document.getElementById('next-page-btn').onclick = () => {
                        if (this.historyCurrentPage < this.historyLastPage) this.fetchHistory(this.historyCurrentPage + 1);
                    };
                },

                closeHistory() {
                    document.getElementById('history-modal').classList.add('hidden');
                },

                fetchHistory(page = 1, search = '') {
                    const tbody = document.getElementById('history-list-body');
                    tbody.innerHTML = '<tr><td colspan="5" class="px-6 py-4 text-center text-slate-400">Loading...</td></tr>';

                    const searchParam = search ? `&search=${search}` : '';

                    fetch(`{{ route('api.pos.history') }}?page=${page}${searchParam}`, {
                        headers: {
                            'Accept': 'application/json',
                            'Authorization': 'Bearer ' + this.apiToken
                        }
                    })
                        .then(res => res.json())
                        .then(data => {
                            this.historyCurrentPage = data.current_page;
                            this.historyLastPage = data.last_page;
                            this.renderHistory(data.data);

                            document.getElementById('prev-page-btn').disabled = data.current_page <= 1;
                            document.getElementById('next-page-btn').disabled = data.current_page >= data.last_page;
                        })
                        .catch(err => {
                            console.error(err);
                            tbody.innerHTML = '<tr><td colspan="5" class="px-6 py-4 text-center text-red-400">Error loading history</td></tr>';
                        });
                },

                renderHistory(sales) {
                    const tbody = document.getElementById('history-list-body');
                    tbody.innerHTML = '';

                    if (sales.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="5" class="px-6 py-8 text-center text-slate-400">No transactions found</td></tr>';
                        return;
                    }

                    sales.forEach(sale => {
                        const date = new Date(sale.created_at).toLocaleString('en-US', {
                            month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit'
                        });

                        const statusColors = {
                            'completed': 'bg-green-100 text-green-700',
                            'void': 'bg-red-100 text-red-700',
                            'pending': 'bg-yellow-100 text-yellow-700'
                        };
                        const statusClass = statusColors[sale.status] || 'bg-slate-100 text-slate-700';

                        const row = `
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <tr class="hover:bg-slate-50 transition-colors group">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">#${sale.id}</td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">${date}</td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-800">${this.formatPrice(sale.total_amount)}</td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <td class="px-6 py-4 whitespace-nowrap">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold ${statusClass} capitalize">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        ${sale.status}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <button class="text-slate-400 hover:text-{{ $theme }}-600 mr-3 hidden">View</button> 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    ${sale.status !== 'void' ? `
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <button onclick="posApp.voidSale(${sale.id})" class="text-slate-400 hover:text-red-600 transition-colors" title="Void Transaction">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </svg>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </button>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    ` : ''}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </tr>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        `;
                        tbody.innerHTML += row;
                    });
                },

                voidSale(saleId) {
                    Swal.fire({
                        title: 'Void Transaction?',
                        text: "This requires supervisor approval.",
                        icon: 'warning',
                        input: 'password',
                        inputPlaceholder: 'Enter Supervisor PIN',
                        inputAttributes: {
                            autocapitalize: 'off',
                            autocorrect: 'off'
                        },
                        showCancelButton: true,
                        confirmButtonText: 'Void Transaction',
                        confirmButtonColor: '#ef4444',
                        showLoaderOnConfirm: true,
                        inputValidator: (value) => {
                            if (!value) {
                                return 'You need to enter the Supervisor PIN!'
                            }
                        },
                        preConfirm: (pin) => {
                            return fetch(`{{ url('/api/pos/sales') }}/${saleId}/void`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Authorization': 'Bearer ' + this.apiToken,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({ pin: pin })
                            })
                                .then(async response => {
                                    if (!response.ok) {
                                        const contentType = response.headers.get("content-type");
                                        if (contentType && contentType.indexOf("application/json") !== -1) {
                                            const data = await response.json();
                                            throw new Error(data.message || 'Failed to void');
                                        } else {
                                            throw new Error("Server returned non-JSON error. Check networking/auth.");
                                        }
                                    }
                                    return response.json()
                                })
                                .catch(error => {
                                    Swal.showValidationMessage(`${error.message}`)
                                })
                        },
                        allowOutsideClick: () => !Swal.isLoading()
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Voided!',
                                text: 'Values have been reverted.',
                                icon: 'success'
                            });
                            this.fetchHistory(this.historyCurrentPage); // Refresh list
                        }
                    })
                },

                // --- Customer Features ---

                openCustomerModal() {
                    document.getElementById('customer-modal').classList.remove('hidden');
                    document.getElementById('customer-search-input').focus();

                    // Setup search listener
                    const searchInput = document.getElementById('customer-search-input');
                    if (searchInput && !searchInput.dataset.listening) {
                        searchInput.addEventListener('input', (e) => {
                            clearTimeout(this.customerSearchTimeout);
                            this.customerSearchTimeout = setTimeout(() => {
                                this.searchCustomers(e.target.value);
                            }, 300);
                        });
                        searchInput.dataset.listening = true;
                    }
                },

                closeCustomerModal() {
                    document.getElementById('customer-modal').classList.add('hidden');
                },

                switchCustomerTab(tab) {
                    const searchTab = document.getElementById('customer-tab-search');
                    const createTab = document.getElementById('customer-tab-create');
                    const searchBtn = document.getElementById('tab-search-btn');
                    const createBtn = document.getElementById('tab-create-btn');

                    if (tab === 'search') {
                        searchTab.classList.remove('hidden');
                        createTab.classList.add('hidden');
                        searchBtn.className = 'flex-1 py-3 text-sm font-bold text-{{ $theme }}-600 border-b-2 border-{{ $theme }}-600 bg-slate-50';
                        createBtn.className = 'flex-1 py-3 text-sm font-medium text-slate-500 hover:text-slate-700';
                        document.getElementById('customer-search-input').focus();
                    } else {
                        searchTab.classList.add('hidden');
                        createTab.classList.remove('hidden');
                        searchBtn.className = 'flex-1 py-3 text-sm font-medium text-slate-500 hover:text-slate-700';
                        createBtn.className = 'flex-1 py-3 text-sm font-bold text-{{ $theme }}-600 border-b-2 border-{{ $theme }}-600 bg-slate-50';
                        document.getElementById('new-customer-name').focus();
                    }
                },

                searchCustomers(query) {
                    const container = document.getElementById('customer-search-results');
                    container.innerHTML = '<p class="text-center text-slate-400 py-4 text-sm">Searching...</p>';

                    if (!query) {
                        container.innerHTML = '<p class="text-center text-slate-400 py-4 text-sm">Start typing to search...</p>';
                        return;
                    }

                    fetch(`{{ route('api.pos.customers.search') }}?query=${query}`, {
                        headers: {
                            'Accept': 'application/json',
                            'Authorization': 'Bearer ' + this.apiToken
                        }
                    })
                        .then(res => res.json())
                        .then(data => {
                            container.innerHTML = '';
                            if (data.length === 0) {
                                container.innerHTML = '<p class="text-center text-slate-400 py-4 text-sm">No customers found.</p>';
                                return;
                            }

                            data.forEach(customer => {
                                const el = document.createElement('div');
                                el.className = 'p-3 hover:bg-slate-50 rounded-lg cursor-pointer border border-transparent hover:border-slate-100 transition-colors flex justify-between items-center group';
                                el.innerHTML = `
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <p class="font-bold text-slate-800">${customer.name}</p>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <p class="text-xs text-slate-500">${customer.phone || 'No Phone'}</p>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <button class="text-{{ $theme }}-600 font-bold text-xs bg-{{ $theme }}-50 px-3 py-1.5 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity">Select</button>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    `;
                                el.onclick = () => this.selectCustomer(customer);
                                container.appendChild(el);
                            });
                        });
                },

                createCustomer() {
                    const name = document.getElementById('new-customer-name').value;
                    const phone = document.getElementById('new-customer-phone').value;
                    const email = document.getElementById('new-customer-email').value;

                    if (!name) {
                        Swal.fire({ title: 'Error', text: 'Name is required', icon: 'error', toast: true, position: 'top', showConfirmButton: false, timer: 3000 });
                        return;
                    }
                    if (!phone) {
                        Swal.fire({ title: 'Error', text: 'Phone number is required', icon: 'error', toast: true, position: 'top', showConfirmButton: false, timer: 3000 });
                        return;
                    }

                    fetch('{{ route('api.pos.customers.create') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Authorization': 'Bearer ' + this.apiToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ name, phone, email })
                    })
                        .then(async res => {
                            const data = await res.json();
                            if (!res.ok) {
                                throw new Error(data.message || 'Failed to create customer');
                            }
                            return data;
                        })
                        .then(data => {
                            this.selectCustomer(data.customer);
                            // Reset form
                            document.getElementById('new-customer-name').value = '';
                            document.getElementById('new-customer-phone').value = '';
                            document.getElementById('new-customer-email').value = '';
                        })
                        .catch(err => {
                            Swal.fire({
                                title: 'Error',
                                text: err.message,
                                icon: 'error',
                                position: 'top',
                                toast: true,
                                timer: 4000
                            });
                        });
                },

                selectCustomer(customer) {
                    this.cartCustomer = customer;
                    localStorage.setItem('pos_customer', JSON.stringify(customer));

                    const nameDisplay = document.getElementById('cart-customer-name');
                    const removeBtn = document.getElementById('remove-customer-btn');

                    if (nameDisplay) {
                        nameDisplay.innerText = customer.name;
                        nameDisplay.classList.add('text-{{ $theme }}-600', 'font-bold');
                    }
                    if (removeBtn) removeBtn.classList.remove('hidden');

                    this.closeCustomerModal();

                    Swal.fire({
                        title: 'Customer Selected',
                        text: `Order linked to ${customer.name}`,
                        icon: 'success',
                        toast: true,
                        position: 'top',
                        showConfirmButton: false,
                        timer: 2000
                    });
                },

                removeCustomer() {
                    this.cartCustomer = null;
                    localStorage.removeItem('pos_customer');

                    const nameDisplay = document.getElementById('cart-customer-name');
                    const removeBtn = document.getElementById('remove-customer-btn');

                    if (nameDisplay) {
                        nameDisplay.innerText = 'Guest Customer';
                        nameDisplay.classList.remove('text-{{ $theme }}-600', 'font-bold');
                    }
                    if (removeBtn) removeBtn.classList.add('hidden');

                    Swal.fire({
                        title: 'Removed',
                        text: 'Customer unlinked from order',
                        icon: 'info',
                        toast: true,
                        position: 'top',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            };

            document.addEventListener('DOMContentLoaded', () => {
                window.posApp = posApp; // Ensure global access for inline onclicks
                posApp.init();
            });
        </script>
    @endpush
@endsection