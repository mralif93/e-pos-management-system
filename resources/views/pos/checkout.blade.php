<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Payment - {{ config('app.name', 'Laravel') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-slate-50 min-h-screen overflow-hidden flex flex-col font-inter antialiased">
    @php $theme = $outletSettings['pos_theme_color'] ?? 'indigo'; @endphp

    <div x-data="checkoutApp()" x-init="init()"
        class="w-full h-[100dvh] flex flex-col lg:flex-row overflow-hidden bg-slate-50">

        <!-- LEFT PANEL: Order Summary (White, Fixed Width on Desktop) -->
        <div
            class="w-full lg:w-[400px] xl:w-[450px] h-auto lg:h-full bg-white border-r border-slate-200 flex flex-col shrink-0 z-10 shadow-sm relative">

            <!-- Header (Back & Title) -->
            <div class="h-16 flex items-center justify-between px-5 border-b border-slate-100 shrink-0">
                <div class="flex items-center gap-3">
                    <button onclick="window.location.href='{{ route('pos.home') }}'"
                        class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-100 text-slate-400 hover:text-slate-700 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                    </button>
                    <div>
                        <h1 class="text-base font-bold text-slate-800 leading-tight">Checkout</h1>
                        <div class="text-[10px] font-medium text-slate-400">ID: #<span x-text="orderId"></span></div>
                    </div>
                </div>
                <!-- Mobile Toggle Summary (Visible only on mobile) -->
                <button @click="showMobileSummary = !showMobileSummary"
                    class="lg:hidden flex items-center gap-2 text-xs font-bold text-{{ $theme }}-600 bg-{{ $theme }}-50 px-3 py-1.5 rounded-lg">
                    <span x-text="showMobileSummary ? 'Hide' : 'Show'"></span>
                    <span x-text="formatPrice(total)"></span>
                    <svg class="w-4 h-4 transition-transform" :class="showMobileSummary ? 'rotate-180' : ''" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
            </div>

            <!-- Collapsible Content (Mobile: Hidden by default, Desktop: Always Visible) -->
            <div class="flex-col flex-1 overflow-hidden transition-all duration-300"
                :class="{'flex': showMobileSummary, 'hidden': !showMobileSummary, 'lg:flex': true}">

                <!-- Customer Info (Compact) -->
                <div x-show="cartCustomer"
                    class="px-5 py-3 bg-slate-50 border-b border-slate-100 flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-2 overflow-hidden">
                        <div
                            class="w-8 h-8 rounded-full bg-{{ $theme }}-100 flex items-center justify-center text-{{ $theme }}-600 font-bold text-xs shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-slate-800 truncate" x-text="cartCustomer.name"></p>
                            <p class="text-[10px] text-slate-500 truncate" x-text="cartCustomer.phone || 'No Phone'">
                            </p>
                        </div>
                    </div>
                    <button class="text-[10px] text-red-500 hover:underline" @click="removeCustomer()">Remove</button>
                </div>

                <!-- Items List (Scrollable) -->
                <div class="flex-1 overflow-y-auto custom-scrollbar p-5 space-y-4">
                    <template x-for="item in cart" :key="item.id">
                        <div class="flex group">
                            <!-- Qty Badge -->
                            <div
                                class="w-8 h-8 rounded-lg bg-slate-50 border border-slate-200 flex items-center justify-center font-bold text-xs text-slate-700 mr-3 shrink-0">
                                <span x-text="item.quantity"></span>
                            </div>

                            <!-- Details -->
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-semibold text-slate-800 leading-snug mb-0.5" x-text="item.name">
                                </h4>
                                <div class="flex items-center gap-2 text-[10px] text-slate-500">
                                    <span x-text="'@ ' + formatPrice(item.price)"></span>
                                </div>
                            </div>

                            <!-- Price -->
                            <div class="text-right pl-2">
                                <span class="block text-sm font-bold text-slate-800"
                                    x-text="formatPrice(item.price * item.quantity)"></span>
                            </div>
                        </div>
                    </template>
                    <div x-show="cart.length === 0" class="text-center py-10 text-slate-400">
                        <p class="text-xs">Cart is empty</p>
                    </div>
                </div>

                <!-- Totals Footer (Fixed at bottom of left panel) -->
                <div class="bg-slate-50 border-t border-slate-200 p-5 shrink-0 space-y-2">
                    <div class="flex justify-between text-xs text-slate-500">
                        <span>Subtotal</span>
                        <span class="font-medium text-slate-700" x-text="formatPrice(subtotal)"></span>
                    </div>
                    <div class="flex justify-between text-xs text-slate-500">
                        <span>Tax (<span x-text="taxRate"></span>%)</span>
                        <span class="font-medium text-slate-700" x-text="formatPrice(taxAmount)"></span>
                    </div>
                    <div x-show="discountAmount > 0" class="flex justify-between text-xs text-green-600 font-medium">
                        <span>Discount</span>
                        <span>- <span x-text="formatPrice(discountAmount)"></span></span>
                    </div>

                    <div class="pt-2 flex gap-2">
                        <button x-show="discountAmount == 0" @click="openPinModal()"
                            class="text-[10px] font-bold text-{{ $theme }}-600 hover:underline flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                                </path>
                            </svg>
                            Add Discount
                        </button>

                        <button x-show="discountAmount == 0" @click="showCouponModal = true"
                            class="text-[10px] font-bold text-{{ $theme }}-600 hover:underline flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z">
                                </path>
                            </svg>
                            Coupon
                        </button>

                        <button x-show="discountAmount > 0" @click="removeDiscount()"
                            class="text-[10px] font-bold text-red-500 hover:underline flex items-center gap-1">
                            Remove Discount <span x-show="appliedCouponCode" x-text="'(' + appliedCouponCode + ')'"
                                class="ml-1 font-mono text-[9px]"></span>
                        </button>
                    </div>

                    <div class="pt-3 mt-1 border-t border-slate-200 flex justify-between items-end">
                        <span class="text-sm font-bold text-slate-800">Total Payable</span>
                        <span class="text-2xl font-black text-{{ $theme }}-600 leading-none"
                            x-text="formatPrice(total)"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT PANEL: Payment Interface (Gray, Flexible) -->
        <div class="flex-1 flex flex-col min-w-0 h-full relative overflow-hidden">

            <!-- Top Bar: Payment Status -->
            <div class="h-16 bg-white border-b border-slate-200 px-6 flex items-center justify-between shrink-0">
                <h2 class="text-sm font-bold text-slate-400 uppercase tracking-wider">Payment</h2>
                <div class="flex gap-4">
                    <div class="text-right">
                        <span class="text-[10px] font-bold text-slate-400 uppercase block">Paid</span>
                        <span class="text-sm font-black text-green-600" x-text="formatPrice(totalPaid)"></span>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] font-bold text-slate-400 uppercase block">Remaining</span>
                        <span class="text-sm font-black text-red-500" x-text="formatPrice(remainingDue)"></span>
                    </div>
                </div>
            </div>

            <!-- Split Content: Methods & Numpad vs Payment List -->
            <div class="flex-1 p-4 lg:p-6 overflow-y-auto flex flex-col md:flex-row gap-6">

                <!-- Center Stage: Inputs & Numpad -->
                <div class="flex-1 flex flex-col gap-4 max-w-xl mx-auto w-full">

                    <!-- Payment Methods Tabs -->
                    <div class="bg-white p-1 rounded-xl border border-slate-200 flex shadow-sm">
                        <template x-for="method in ['cash', 'card', 'qr']">
                            <button @click="paymentMethod = method"
                                :class="paymentMethod === method ? 'bg-{{ $theme }}-600 text-white shadow-md' : 'text-slate-500 hover:bg-slate-50'"
                                class="flex-1 py-2 rounded-lg text-xs font-bold uppercase tracking-wide transition-all duration-200"
                                x-text="method.replace('qr', 'QR Pay')">
                            </button>
                        </template>
                    </div>

                    <!-- Amount Display -->
                    <div class="bg-white border-2 rounded-xl p-4 text-right flex flex-col justify-center h-20 shadow-sm transition-all"
                        :class="tenderAmount > remainingDue && remainingDue > 0 ? 'border-orange-200 ring-4 ring-orange-50' : 'border-slate-200 focus-within:border-{{ $theme }}-400 focus-within:ring-4 focus-within:ring-{{ $theme }}-50'">
                        <span class="text-[10px] font-bold text-slate-400 uppercase mb-1">Enter Amount</span>
                        <div class="flex items-baseline justify-end gap-1">
                            <span class="text-lg text-slate-400 font-medium" x-text="currency"></span>
                            <span class="text-3xl font-black text-slate-800 tracking-tight"
                                x-text="tenderAmountDisplay || '0'"></span>
                        </div>
                    </div>

                    <!-- Numpad -->
                    <div class="grid grid-cols-4 gap-2 bg-white p-3 rounded-xl border border-slate-200 shadow-sm">
                        <!-- Numbers -->
                        <div class="col-span-3 grid grid-cols-3 gap-2">
                            <template x-for="n in [1,2,3,4,5,6,7,8,9]">
                                <button @click="appendNumber(n)"
                                    class="h-10 rounded-lg bg-slate-50 hover:bg-white border border-slate-100 hover:border-{{ $theme }}-300 text-lg font-bold text-slate-700 shadow-sm hover:shadow active:scale-95 transition-all"
                                    x-text="n"></button>
                            </template>
                            <button @click="appendNumber('.')"
                                class="h-10 rounded-lg bg-slate-50 hover:bg-white border border-slate-100 hover:border-{{ $theme }}-300 text-lg font-bold text-slate-700 shadow-sm hover:shadow active:scale-95 transition-all">.</button>
                            <button @click="appendNumber(0)"
                                class="h-10 rounded-lg bg-slate-50 hover:bg-white border border-slate-100 hover:border-{{ $theme }}-300 text-lg font-bold text-slate-700 shadow-sm hover:shadow active:scale-95 transition-all">0</button>
                            <button @click="backspace()"
                                class="h-10 rounded-lg bg-slate-100 hover:bg-slate-200 border border-transparent text-slate-600 active:scale-95 transition-all flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M3 12l6.414 6.414a2 2 0 001.414.586H19a2 2 0 002-2V7a2 2 0 00-2-2h-8.172a2 2 0 00-1.414.586L3 12z">
                                    </path>
                                </svg>
                            </button>
                        </div>
                        <!-- Quick Actions -->
                        <div class="flex flex-col gap-2">
                            <button @click="setExact()"
                                class="flex-1 rounded-lg bg-{{ $theme }}-50 hover:bg-{{ $theme }}-100 text-{{ $theme }}-700 font-bold text-[10px] uppercase border border-{{ $theme }}-200 active:scale-95 transition-all">Exact</button>
                            <button @click="addAmount(10)"
                                class="flex-1 rounded-lg bg-white hover:bg-slate-50 text-slate-600 font-bold text-[10px] border border-slate-200 active:scale-95 transition-all">+10</button>
                            <button @click="addAmount(50)"
                                class="flex-1 rounded-lg bg-white hover:bg-slate-50 text-slate-600 font-bold text-[10px] border border-slate-200 active:scale-95 transition-all">+50</button>
                        </div>
                    </div>

                    <!-- Add Payment Button (Moved) -->
                    <button @click="addPayment()" :disabled="tenderAmount <= 0"
                        class="w-full py-3 rounded-xl font-bold text-white shadow-lg flex items-center justify-center gap-2 transition-all active:scale-95 disabled:opacity-50 disabled:shadow-none disabled:cursor-not-allowed"
                        :class="tenderAmount > 0 ? 'bg-slate-800 hover:bg-slate-900 shadow-slate-200' : 'bg-slate-300'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                            </path>
                        </svg>
                        <span>Add Payment</span>
                    </button>
                </div>

                <!-- Right Side: Payments List (Desktop) / Below (Mobile) -->
                <div class="w-full md:w-72 shrink-0 flex flex-col gap-4">


                    <!-- Payments List Card -->
                    <div
                        class="bg-white rounded-xl border border-slate-200 shadow-sm flex-1 flex flex-col overflow-hidden min-h-[200px]">
                        <div class="p-3 border-b border-slate-100 bg-slate-50">
                            <h3 class="text-[10px] font-bold text-slate-500 uppercase">Transactions</h3>
                        </div>
                        <div class="flex-1 overflow-y-auto p-3 space-y-2">
                            <template x-for="(payment, index) in payments" :key="index">
                                <div
                                    class="flex justify-between items-center p-3 bg-white border border-slate-100 rounded-lg shadow-sm hover:border-slate-300 transition-all group">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 rounded bg-slate-100 flex items-center justify-center text-slate-500">
                                            <svg x-show="payment.method === 'cash'" class="w-4 h-4" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                                                </path>
                                            </svg>
                                            <svg x-show="payment.method === 'card'" class="w-4 h-4" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                                                </path>
                                            </svg>
                                            <svg x-show="payment.method === 'qr'" class="w-4 h-4" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z">
                                                </path>
                                            </svg>
                                        </div>
                                        <span class="text-xs font-bold text-slate-700 capitalize"
                                            x-text="payment.method"></span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="text-sm font-bold text-slate-800"
                                            x-text="formatPrice(payment.amount)"></span>
                                        <button @click="removePayment(index)"
                                            class="text-slate-300 hover:text-red-500 transition-colors opacity-0 group-hover:opacity-100">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </template>
                            <div x-show="payments.length === 0"
                                class="h-full flex flex-col items-center justify-center text-slate-300 opacity-50 min-h-[100px]">
                                <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                                <span class="text-[10px]">No payments yet</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Bottom Bar: Complete Button -->
            <div class="p-4 lg:p-6 bg-white border-t border-slate-200 shrink-0">
                <button @click="processPayment()" :disabled="remainingDue > 0"
                    :class="remainingDue > 0 ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : 'bg-{{ $theme }}-600 hover:bg-{{ $theme }}-700 text-white shadow-sm shadow-{{ $theme }}-200'"
                    class="w-full h-12 rounded-xl font-bold text-base flex items-center justify-center gap-3 transition-all active:scale-[0.98]">
                    <span
                        x-text="remainingDue > 0 ? 'Balance Remaining: ' + formatPrice(remainingDue) : 'Complete Order'"></span>
                    <svg x-show="remainingDue <= 0" class="w-6 h-6 animate-pulse" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </button>
            </div>
        </div>




        <!-- PIN Modal -->
        <div x-show="showPinModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4 transition-opacity"
            x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak>
            <div @click.away="showPinModal = false"
                class="bg-white rounded-[24px] shadow-2xl border border-slate-100 w-full max-w-[340px] overflow-hidden transform transition-all"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">

                <div class="p-6 text-center">
                    <div
                        class="w-12 h-12 bg-{{ $theme }}-50 rounded-full flex items-center justify-center mx-auto mb-4 text-{{ $theme }}-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-black text-slate-800 mb-1">Manager Access</h3>
                    <p class="text-xs text-slate-500 mb-6">Enter PIN to authorize discount.</p>

                    <input type="password" x-model="pinInput" placeholder="••••" autofocus
                        class="w-full text-center text-2xl tracking-[0.5em] font-black border-b-2 border-slate-200 focus:border-{{ $theme }}-500 outline-none py-2 mb-8 bg-transparent transition-colors placeholder:tracking-normal placeholder:text-slate-300">

                    <div class="grid grid-cols-2 gap-3">
                        <button @click="showPinModal = false"
                            class="py-3 px-4 rounded-xl font-bold text-slate-500 hover:bg-slate-50 transition-colors text-sm">Cancel</button>
                        <button @click="verifyPin()"
                            class="py-3 px-4 rounded-xl font-bold bg-{{ $theme }}-600 text-white hover:bg-{{ $theme }}-700 shadow-lg shadow-{{ $theme }}-200 transition-all text-sm">Verify</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Discount Modal -->
        <div x-show="showDiscountModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4 transition-opacity"
            x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak>
            <div @click.away="showDiscountModal = false"
                class="bg-white rounded-[24px] shadow-2xl border border-slate-100 w-full max-w-[340px] overflow-hidden transform transition-all"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">

                <div class="p-6">
                    <div class="text-center mb-6">
                        <h3 class="text-lg font-black text-slate-800">Add Discount</h3>
                        <p class="text-xs text-slate-500">Apply custom discount to this order.</p>
                    </div>

                    <div class="flex bg-slate-100 p-1.5 rounded-xl mb-6">
                        <button @click="tempDiscountType = 'amount'"
                            :class="tempDiscountType === 'amount' ? 'bg-white shadow-sm text-{{ $theme }}-700' : 'text-slate-500 hover:text-slate-700'"
                            class="flex-1 py-2 rounded-lg text-xs font-bold transition-all">Currency ($)</button>
                        <button @click="tempDiscountType = 'percent'"
                            :class="tempDiscountType === 'percent' ? 'bg-white shadow-sm text-{{ $theme }}-700' : 'text-slate-500 hover:text-slate-700'"
                            class="flex-1 py-2 rounded-lg text-xs font-bold transition-all">Percentage (%)</button>
                    </div>

                    <div class="space-y-4 mb-8">
                        <div>
                            <label
                                class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 ml-1">Value</label>
                            <input type="number" x-model="tempDiscountValue" placeholder="0.00"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold text-slate-800 focus:border-{{ $theme }}-500 focus:ring-0 outline-none transition-all placeholder:font-normal">
                        </div>
                        <div>
                            <label
                                class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 ml-1">Reason</label>
                            <input type="text" x-model="tempDiscountReason" placeholder="e.g. Promo, Staff"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-medium text-slate-800 focus:border-{{ $theme }}-500 focus:ring-0 outline-none transition-all text-sm placeholder:font-normal">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <button @click="showDiscountModal = false"
                            class="py-3 px-4 rounded-xl font-bold text-slate-500 hover:bg-slate-50 transition-colors text-sm">Cancel</button>
                        <button @click="applyDiscount()"
                            class="py-3 px-4 rounded-xl font-bold bg-{{ $theme }}-600 text-white hover:bg-{{ $theme }}-700 shadow-lg shadow-{{ $theme }}-200 transition-all text-sm">Apply</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Coupon Modal -->
        <div x-show="showCouponModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4 transition-opacity"
            x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak>
            <div @click.away="showCouponModal = false"
                class="bg-white rounded-[24px] shadow-2xl border border-slate-100 w-full max-w-[340px] overflow-hidden transform transition-all"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">

                <div class="p-6">
                    <div class="text-center mb-6">
                        <div
                            class="w-12 h-12 bg-{{ $theme }}-50 rounded-full flex items-center justify-center mx-auto mb-4 text-{{ $theme }}-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-black text-slate-800">Apply Coupon</h3>
                        <p class="text-xs text-slate-500">Enter a valid coupon code.</p>
                    </div>

                    <div class="mb-8">
                        <input type="text" x-model="couponInput" placeholder="Enter Code (e.g. SAVE10)"
                            class="w-full text-center uppercase text-xl font-bold tracking-widest bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:border-{{ $theme }}-500 focus:ring-0 outline-none transition-all placeholder:tracking-normal placeholder:font-normal placeholder:text-sm">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <button @click="showCouponModal = false"
                            class="py-3 px-4 rounded-xl font-bold text-slate-500 hover:bg-slate-50 transition-colors text-sm">Cancel</button>
                        <button @click="verifyCoupon()"
                            class="py-3 px-4 rounded-xl font-bold bg-{{ $theme }}-600 text-white hover:bg-{{ $theme }}-700 shadow-lg shadow-{{ $theme }}-200 transition-all text-sm">Apply
                            Code</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hidden Receipt Print Area -->
        <div id="receipt-print-area" class="hidden">
            <template x-if="lastSale">
                <div class="p-4 bg-white text-black font-mono text-xs w-[80mm] mx-auto">
                    <!-- Header -->
                    <div class="text-center mb-4">
                        <h2 class="text-lg font-bold uppercase">{{ $outletSettings['name'] ?? 'POS Outlet' }}</h2>
                        <p>{{ $outletSettings['address'] ?? 'Store Address' }}</p>
                        <p>Tel: {{ $outletSettings['phone'] ?? '-' }}</p>
                    </div>

                    <!-- Meta -->
                    <div class="mb-4 border-b border-black pb-2 border-dashed">
                        <div class="flex justify-between">
                            <span>Date:</span>
                            <span x-text="new Date(lastSale.created_at).toLocaleString()"></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Receipt #:</span>
                            <span x-text="lastSale.id"></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Cashier:</span>
                            <span x-text="lastSale.user ? lastSale.user.name : '-'"></span>
                        </div>
                        <template x-if="lastSale.customer">
                            <div class="flex justify-between">
                                <span>Customer:</span>
                                <span x-text="lastSale.customer.name"></span>
                            </div>
                        </template>
                    </div>

                    <!-- Items -->
                    <div class="mb-4 text-left">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-black border-dashed">
                                    <th class="py-1 text-left">Item</th>
                                    <th class="py-1 text-right">Qty</th>
                                    <th class="py-1 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="border-b border-black border-dashed">
                                <template x-for="item in lastSale.sale_items" :key="item.id">
                                    <tr>
                                        <td class="py-1">
                                            <div x-text="item.product ? item.product.name : 'Unknown Item'"></div>
                                            <div x-show="item.price != item.total" class="text-[10px] text-slate-500"
                                                x-text="'@ ' + parseFloat(item.price).toFixed(2)"></div>
                                        </td>
                                        <td class="py-1 text-right" x-text="item.quantity"></td>
                                        <td class="py-1 text-right"
                                            x-text="parseFloat(item.price * item.quantity).toFixed(2)"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <!-- Totals -->
                    <div class="mb-4 space-y-1">
                        <div class="flex justify-between">
                            <span>Subtotal:</span>
                            <span x-text="formatPrice(calculateSubtotal(lastSale))"></span>
                        </div>

                        <template x-if="parseFloat(lastSale.discount_amount) > 0">
                            <div class="flex justify-between">
                                <span>Discount:</span>
                                <span>- <span x-text="formatPrice(lastSale.discount_amount)"></span></span>
                            </div>
                        </template>

                        <template x-if="parseFloat(lastSale.tax_amount) > 0">
                            <div class="flex justify-between">
                                <span>Tax ({{ $outletSettings['tax_rate'] ?? 0 }}%):</span>
                                <span x-text="formatPrice(lastSale.tax_amount)"></span>
                            </div>
                        </template>

                        <div
                            class="flex justify-between font-bold text-sm border-t border-black border-dashed pt-2 mt-2">
                            <span>TOTAL:</span>
                            <span x-text="formatPrice(lastSale.total_amount)"></span>
                        </div>
                    </div>

                    <!-- Payments -->
                    <div class="mb-4 border-t border-black border-dashed pt-2">
                        <template x-for="pay in lastSale.payments" :key="pay.id">
                            <div class="flex justify-between">
                                <span class="capitalize" x-text="pay.payment_method"></span>
                                <span x-text="formatPrice(pay.amount)"></span>
                            </div>
                        </template>
                        <div class="flex justify-between font-bold mt-1">
                            <span>Change:</span>
                            <span x-text="formatPrice(calculateChange(lastSale))"></span>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="text-center mt-6 pt-2 border-t border-black border-dashed">
                        <p class="font-bold">Thank You!</p>
                        <p class="text-[10px]">Please come again.</p>
                    </div>
                </div>
            </template>
        </div>

    </div>

    <script>
        function checkoutApp() {
            return {
                apiToken: '{{ $apiToken }}',
                userId: {{ auth()->id() }},
                outletId: {{ auth()->user()->outlet_id }},
                cart: [],
                currency: '{{ $outletSettings['currency_symbol'] ?? '$' }}',
                taxRate: {{ $outletSettings['tax_rate'] ?? 0 }},
                subtotal: 0,
                taxAmount: 0,
                total: 0,
                orderId: Math.floor(1000 + Math.random() * 9000),
                showMobileSummary: false,

                paymentMethod: 'cash',
                tenderAmount: 0,
                tenderAmountDisplay: '',
                payments: [],

                // Customer State
                cartCustomer: null,

                // Discount State
                discountAmount: 0,
                discountReason: '',
                showPinModal: false,
                pinInput: '',
                showDiscountModal: false,
                tempDiscountType: 'amount',
                tempDiscountValue: '',
                tempDiscountReason: '',

                // Coupon State
                showCouponModal: false,
                couponInput: '',
                appliedCouponCode: null,

                // Receipt State
                lastSale: null,

                init() {
                    window.posApp = this;
                    const storedCart = localStorage.getItem('pos_cart');
                    if (storedCart) {
                        this.cart = JSON.parse(storedCart);
                        this.calculateTotals();
                        this.tenderAmount = this.total;
                        this.tenderAmountDisplay = this.total.toFixed(2);
                    }
                    const storedCustomer = localStorage.getItem('pos_customer');
                    if (storedCustomer) {
                        this.cartCustomer = JSON.parse(storedCustomer);
                    }
                },

                calculateTotals() {
                    this.subtotal = this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                    this.taxAmount = parseFloat((this.subtotal * (this.taxRate / 100)).toFixed(2));
                    // Ensure total doesn't go below 0
                    this.total = parseFloat(Math.max(0, this.subtotal + this.taxAmount - this.discountAmount).toFixed(2));
                },

                // Discount Logic
                openPinModal() {
                    this.pinInput = '';
                    this.showPinModal = true;
                },

                async verifyPin() {
                    if (!this.pinInput) return;

                    try {
                        const response = await fetch('{{ route('api.pos.verify-manager-pin') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Authorization': 'Bearer ' + this.apiToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ pin: this.pinInput })
                        });

                        if (response.ok) {
                            this.showPinModal = false;
                            this.showDiscountModal = true;
                            this.tempDiscountValue = '';
                            this.tempDiscountReason = '';
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Invalid PIN',
                                timer: 1500,
                                showConfirmButton: false,
                                customClass: { popup: 'rounded-[24px] shadow-2xl' }
                            });
                        }
                    } catch (e) {
                        console.error(e);
                        Swal.fire({ icon: 'error', title: 'Error verifying PIN' });
                    }
                },

                openDiscountModal() {
                    this.showDiscountModal = true;
                    this.tempDiscountValue = '';
                    this.tempDiscountReason = '';
                },

                applyDiscount() {
                    let val = parseFloat(this.tempDiscountValue);
                    if (isNaN(val) || val < 0) return;

                    let calculatedDiscount = 0;
                    if (this.tempDiscountType === 'percent') {
                        calculatedDiscount = (this.subtotal + this.taxAmount) * (val / 100);
                    } else {
                        calculatedDiscount = val;
                    }

                    this.discountAmount = calculatedDiscount;
                    this.discountReason = this.tempDiscountReason;
                    this.calculateTotals();
                    this.showDiscountModal = false;

                    // Reset payments if total changed
                    this.payments = [];
                    this.tenderAmount = this.total;
                    this.tenderAmountDisplay = this.total.toFixed(2);
                },

                removeDiscount() {
                    this.discountAmount = 0;
                    this.discountReason = '';
                    this.appliedCouponCode = null;
                    this.calculateTotals();
                    this.payments = [];
                    this.tenderAmount = this.total;
                    this.tenderAmountDisplay = this.total.toFixed(2);
                },

                async verifyCoupon() {
                    if (!this.couponInput) return;

                    try {
                        const response = await fetch('{{ route('api.pos.verify-coupon') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Authorization': 'Bearer ' + this.apiToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ code: this.couponInput, amount: this.total + this.discountAmount }) // Send raw total before existing discount
                        });

                        const data = await response.json();

                        if (response.ok && data.valid) {
                            this.discountAmount = parseFloat(data.discount_amount);
                            this.discountReason = 'Coupon: ' + data.coupon.code;
                            this.appliedCouponCode = data.coupon.code;
                            this.showCouponModal = false;
                            this.couponInput = '';

                            this.calculateTotals();
                            this.payments = [];
                            this.tenderAmount = this.total;
                            this.tenderAmountDisplay = this.total.toFixed(2);

                            Swal.fire({
                                icon: 'success',
                                title: 'Coupon Applied!',
                                text: `Saved ${this.formatPrice(data.discount_amount)}`,
                                timer: 1500,
                                showConfirmButton: false,
                                customClass: { popup: 'rounded-[24px] shadow-2xl' }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Invalid Coupon',
                                text: data.message || 'Code not found',
                                customClass: { popup: 'rounded-[24px] shadow-2xl' }
                            });
                        }
                    } catch (e) {
                        console.error(e);
                        Swal.fire({ icon: 'error', title: 'Error verifying coupon' });
                    }
                },

                // ... Existing Getters ...
                get totalPaid() {
                    return this.payments.reduce((sum, p) => sum + p.amount, 0);
                },

                get remainingDue() {
                    return Math.max(0, this.total - this.totalPaid);
                },

                get changeAmount() {
                    return this.totalPaid - this.total;
                },

                formatPrice(amount) {
                    return this.currency + parseFloat(amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                },

                appendNumber(num) {
                    if (num === '.' && this.tenderAmountDisplay.includes('.')) return;

                    // Limit to 2 decimal places
                    if (this.tenderAmountDisplay.includes('.')) {
                        const parts = this.tenderAmountDisplay.split('.');
                        if (parts[1] && parts[1].length >= 2) return;
                    }

                    if (this.tenderAmountDisplay === '' && num === '.') this.tenderAmountDisplay = '0.';
                    else this.tenderAmountDisplay += num.toString();
                    this.tenderAmount = parseFloat(this.tenderAmountDisplay);
                },

                backspace() {
                    this.tenderAmountDisplay = this.tenderAmountDisplay.slice(0, -1);
                    this.tenderAmount = this.tenderAmountDisplay ? parseFloat(this.tenderAmountDisplay) : 0;
                },

                setExact() {
                    this.tenderAmount = this.remainingDue;
                    this.tenderAmountDisplay = this.remainingDue.toFixed(2);
                },

                addAmount(amount) {
                    this.tenderAmount += amount;
                    this.tenderAmountDisplay = this.tenderAmount.toFixed(2);
                },

                addPayment() {
                    if (this.tenderAmount <= 0) return;

                    this.payments.push({
                        method: this.paymentMethod,
                        amount: this.tenderAmount
                    });

                    // Reset for next entry
                    this.tenderAmount = 0;
                    this.tenderAmountDisplay = '';

                    // Specific logic: if remaining, suggest remaining
                    if (this.remainingDue > 0) {
                        this.setExact();
                    }
                },

                removePayment(index) {
                    this.payments.splice(index, 1);
                    if (this.payments.length === 0) {
                        this.setExact();
                    }
                },

                finishOrder() {
                    localStorage.removeItem('pos_cart');
                    localStorage.removeItem('pos_customer');
                    window.location.href = '{{ route('pos.home') }}';
                },

                // Receipt Helpers
                calculateSubtotal(sale) {
                    // Logic depends on tax inclusive/exclusive. Assuming our current logic:
                    // Total = Subtotal + Tax - Discount.
                    // So Subtotal = items sum (price * qty) as stored or recalculated.
                    // Let's sum items from sale_items
                    return sale.sale_items.reduce((sum, item) => sum + (parseFloat(item.price) * item.quantity), 0);
                },

                calculateChange(sale) {
                    const paid = sale.payments.reduce((sum, p) => sum + parseFloat(p.amount), 0);
                    return Math.max(0, paid - parseFloat(sale.total_amount));
                },



                printReceipt(saleId = null) {
                    const id = saleId || (this.lastSale ? this.lastSale.id : null);
                    if (!id) return;

                    const url = `{{ url('/pos/sales') }}/${id}/receipt-pdf`;
                    window.open(url, '_blank', 'width=400,height=600');
                },

                processPayment() {
                    if (this.remainingDue > 0.01) return; // Float tolerance

                    // 1. Processing State
                    Swal.fire({
                        html: `
                            <div class="py-6">
                                <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-{{ $theme }}-100 border-t-{{ $theme }}-600 mb-4"></div>
                                <h3 class="text-xl font-bold text-slate-800">Processing Payment</h3>
                                <p class="text-sm text-slate-500 mt-2">Connecting to secure gateway...</p>
                            </div>
                        `,
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        width: 400,
                        padding: '2rem',
                        customClass: { popup: 'rounded-[24px] shadow-2xl' },

                    });

                    // Construct Payload
                    const payload = {
                        outlet_id: this.outletId,
                        user_id: this.userId,
                        customer_id: this.cartCustomer ? this.cartCustomer.id : null,
                        total_amount: this.total,
                        tax_amount: this.taxAmount,
                        discount_amount: this.discountAmount,
                        discount_reason: this.discountReason,
                        status: 'completed',
                        items: this.cart.map(item => ({
                            product_id: item.id,
                            quantity: item.quantity,
                            price: item.price
                        })),
                        payments: this.payments.map(p => ({
                            amount: p.amount,
                            payment_method: p.method
                        }))
                    };

                    // Offline Mode Handling
                    const isForcedOffline = localStorage.getItem('pos_forced_offline') === 'true';
                    if (!navigator.onLine || isForcedOffline) {
                        const tempId = 'OFF-' + Date.now();
                        const offlineSale = { ...payload, id: tempId, created_at: new Date().toISOString(), is_offline: true };

                        const offlineSales = JSON.parse(localStorage.getItem('pos_offline_sales') || '[]');
                        offlineSales.push(offlineSale);
                        localStorage.setItem('pos_offline_sales', JSON.stringify(offlineSales));

                        this.lastSale = offlineSale;

                        Swal.fire({
                            icon: 'warning',
                            html: `
                                <div class="w-full p-6 text-center">
                                    <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <h3 class="text-xl font-black text-slate-800 mb-1">Sale Queued (Offline)</h3>
                                    <p class="text-xs text-slate-500">Sales will sync when online.</p>
                                    
                                    <div class="bg-slate-50 rounded-lg p-3 mt-4 mb-4 border border-slate-100">
                                         <div class="flex justify-between items-center text-xs text-slate-600 mb-1">
                                            <span>Amount Paid</span>
                                            <span class="font-bold">${this.tenderAmountDisplay ? this.formatPrice(this.tenderAmount) : this.formatPrice(this.total)}</span>
                                        </div>
                                        <div class="flex justify-between items-center text-base text-slate-800 font-bold border-t border-dashed border-slate-200 pt-1">
                                            <span>Change Due</span>
                                            <span class="text-{{ $theme }}-600">${this.formatPrice(Math.max(0, this.changeAmount))}</span>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 gap-3">
                                        <button onclick="posApp.finishOrder()" class="w-full bg-{{ $theme }}-600 hover:bg-{{ $theme }}-700 text-white font-bold py-3 rounded-xl text-sm shadow-md shadow-{{ $theme }}-200 transition-all">
                                            New Order
                                        </button>
                                    </div>
                                </div>
                            `,
                            showConfirmButton: false,
                            allowOutsideClick: false,
                            customClass: { popup: 'rounded-[24px] shadow-2xl overflow-hidden' },
                            padding: 0,
                            width: 400
                        });
                        return;
                    }

                    fetch('{{ route('api.pos.sales') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Authorization': 'Bearer ' + this.apiToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(err => { throw err; });
                            }
                            return response.json();
                        })
                        .then(data => {
                            // Store last sale for receipt
                            this.lastSale = data.sale;

                            // 2. Success State
                            Swal.fire({
                                html: `
                                <div class="w-full p-6 text-center">
                                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <h3 class="text-xl font-black text-slate-800 mb-1">Payment Successful!</h3>
                                    <p class="text-xs text-slate-500">Transaction #${data.sale.id}</p>
                                    
                                    <div class="bg-slate-50 rounded-lg p-3 mt-4 mb-4 border border-slate-100">
                                        <div class="flex justify-between items-center text-xs text-slate-600 mb-1">
                                            <span>Amount Paid</span>
                                            <span class="font-bold">${this.tenderAmountDisplay ? this.formatPrice(this.tenderAmount) : this.formatPrice(this.total)}</span>
                                        </div>
                                        <div class="flex justify-between items-center text-base text-slate-800 font-bold border-t border-dashed border-slate-200 pt-1">
                                            <span>Change Due</span>
                                            <span class="text-{{ $theme }}-600">${this.formatPrice(Math.max(0, this.changeAmount))}</span>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-3">
                                        <button onclick="posApp.printReceipt()" class="w-full bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold py-3 rounded-xl text-sm transition-all flex items-center justify-center gap-2">
                                           <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2-4h6a2 2 0 012 2v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6a2 2 0 012-2zm9-2V4a2 2 0 00-2-2h-5l-5 5v3m9-3h-2M9 13H5a2 2 0 00-2 2v4a2 2 0 002 2h4a2 2 0 002-2v-4a2 2 0 00-2-2z"></path></svg>
                                           Print
                                        </button>
                                        <button onclick="posApp.finishOrder()" class="w-full bg-{{ $theme }}-600 hover:bg-{{ $theme }}-700 text-white font-bold py-3 rounded-xl text-sm shadow-md shadow-{{ $theme }}-200 transition-all">
                                            New Order
                                        </button>
                                    </div>
                                </div>
                            `,
                                showConfirmButton: false, // Hide default button to remove extra spacing
                                allowOutsideClick: false,
                                customClass: {
                                    popup: 'rounded-[24px] shadow-2xl overflow-hidden'
                                },
                                padding: 0,
                                width: 400
                            });
                        })
                        .catch(error => {
                            console.error('Payment Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Payment Failed',
                                text: error.message || 'An error occurred while processing the payment.',
                                customClass: { popup: 'rounded-xl shadow-xl' },
                                heightAuto: false
                            });
                        });
                }
            }
        }
    </script>
</body>

</html>