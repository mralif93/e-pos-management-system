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

<body class="bg-slate-100 h-screen overflow-hidden flex items-center justify-center p-4">
    @php $theme = $outletSettings['pos_theme_color'] ?? 'indigo'; @endphp
    <div x-data="checkoutApp()" x-init="init()"
        class="bg-white w-full max-w-7xl h-full max-h-[90vh] rounded-3xl shadow-2xl flex overflow-hidden ring-1 ring-black/5">

        <!-- Left: Order Summary -->
        <div class="w-1/3 border-r border-slate-100 bg-white flex flex-col relative z-20">
            <div class="p-8 pt-10">
                <div class="flex items-center gap-4 mb-1">
                    <button onclick="window.location.href='{{ route('pos.home') }}'"
                        class="text-slate-400 hover:text-slate-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                    </button>
                    <h2 class="text-2xl font-bold text-slate-800">Order Summary</h2>
                </div>
                <div class="ml-10">
                    <span
                        class="px-2.5 py-0.5 bg-{{ $theme }}-50 text-{{ $theme }}-700 text-xs font-bold rounded-md tracking-wide">#<span
                            x-text="orderId"></span></span>
                    <div x-show="cartCustomer" class="mt-2 text-sm text-slate-500 font-medium">
                        Customer: <span class="text-slate-800 font-bold" x-text="cartCustomer.name"></span>
                    </div>
                </div>
            </div>

            <div class="flex-grow overflow-y-auto custom-scrollbar px-8 pb-4 space-y-6">
                <template x-for="item in cart" :key="item.id">
                    <div class="flex justify-between items-center group">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center font-bold text-slate-600 text-sm"
                                x-text="item.quantity"></div>
                            <div>
                                <p class="font-bold text-slate-800 text-base" x-text="item.name"></p>
                                <p class="text-xs text-slate-400 font-medium" x-text="'@ ' + formatPrice(item.price)">
                                </p>
                            </div>
                        </div>
                        <span class="font-bold text-slate-800" x-text="formatPrice(item.price * item.quantity)"></span>
                    </div>
                </template>
            </div>

            <div class="p-8 pt-4 bg-white mt-auto">
                <div class="space-y-3 mb-6 border-t border-dashed border-slate-100 pt-6">
                    <div class="flex justify-between text-sm text-slate-500 font-medium">
                        <span>Subtotal</span>
                        <span class="text-slate-700" x-text="formatPrice(subtotal)"></span>
                    </div>
                    <div class="flex justify-between text-sm text-slate-500 font-medium">
                        <span>Service Tax (<span x-text="taxRate"></span>%)</span>
                        <span class="text-slate-700" x-text="formatPrice(taxAmount)"></span>
                    </div>
                    <div x-show="discountAmount > 0" class="flex justify-between text-sm text-green-600 font-bold">
                        <span>Discount <span x-show="discountReason" x-text="'(' + discountReason + ')'"></span></span>
                        <span>- <span x-text="formatPrice(discountAmount)"></span></span>
                    </div>
                </div>

                <!-- Add Discount Button -->
                <div class="mb-4">
                    <button x-show="discountAmount == 0" @click="openPinModal()"
                        class="w-full py-3 rounded-xl border border-dashed border-{{ $theme }}-300 text-{{ $theme }}-600 font-bold hover:bg-{{ $theme }}-50 transition-colors flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                            </path>
                        </svg>
                        Add Discount
                    </button>
                    <button x-show="discountAmount > 0" @click="removeDiscount()"
                        class="w-full py-3 rounded-xl border border-dashed border-red-300 text-red-500 font-bold hover:bg-red-50 transition-colors flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                            </path>
                        </svg>
                        Remove Discount
                    </button>
                </div>

                <div class="flex justify-between items-end border-t border-slate-100 pt-6">
                    <span class="text-slate-800 font-bold text-lg">Total Payable</span>
                    <span class="text-3xl font-black text-{{ $theme }}-600 tracking-tight"
                        x-text="formatPrice(total)"></span>
                </div>
            </div>
        </div>

        <!-- PIN Modal -->
        <div x-show="showPinModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" x-cloak>
            <div @click.away="showPinModal = false"
                class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-sm transform transition-all">
                <h3 class="text-lg font-bold text-slate-800 mb-4 text-center">Manager Authorization</h3>
                <p class="text-sm text-slate-500 mb-4 text-center">Please enter Manager PIN to apply discount</p>

                <input type="password" x-model="pinInput" placeholder="Enter PIN" autofocus
                    class="w-full text-center text-2xl tracking-widest font-bold border-2 border-slate-200 rounded-xl p-3 mb-6 focus:border-{{ $theme }}-500 focus:ring-0 outline-none transition-colors">

                <div class="grid grid-cols-2 gap-3">
                    <button @click="showPinModal = false"
                        class="py-3 font-bold text-slate-500 hover:bg-slate-50 rounded-xl transition-colors">Cancel</button>
                    <button @click="verifyPin()"
                        class="py-3 font-bold bg-{{ $theme }}-600 text-white hover:bg-{{ $theme }}-700 rounded-xl shadow-lg transition-colors">Verify</button>
                </div>
            </div>
        </div>

        <!-- Discount Modal -->
        <div x-show="showDiscountModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" x-cloak>
            <div @click.away="showDiscountModal = false"
                class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-sm transform transition-all">
                <h3 class="text-lg font-bold text-slate-800 mb-4">Apply Discount</h3>

                <div class="flex bg-slate-100 p-1 rounded-xl mb-4">
                    <button @click="tempDiscountType = 'amount'"
                        :class="{'bg-white shadow-sm text-slate-800': tempDiscountType === 'amount', 'text-slate-500': tempDiscountType !== 'amount'}"
                        class="flex-1 py-2 text-sm font-bold rounded-lg transition-all">Amount ($)</button>
                    <button @click="tempDiscountType = 'percent'"
                        :class="{'bg-white shadow-sm text-slate-800': tempDiscountType === 'percent', 'text-slate-500': tempDiscountType !== 'percent'}"
                        class="flex-1 py-2 text-sm font-bold rounded-lg transition-all">Percentage (%)</button>
                </div>

                <div class="space-y-4 mb-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">Value</label>
                        <input type="number" x-model="tempDiscountValue"
                            class="w-full border border-slate-200 rounded-xl p-3 font-bold text-lg focus:border-{{ $theme }}-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">Reason (Optional)</label>
                        <input type="text" x-model="tempDiscountReason"
                            class="w-full border border-slate-200 rounded-xl p-3 text-sm focus:border-{{ $theme }}-500 outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <button @click="showDiscountModal = false"
                        class="py-3 font-bold text-slate-500 hover:bg-slate-50 rounded-xl transition-colors">Cancel</button>
                    <button @click="applyDiscount()"
                        class="py-3 font-bold bg-{{ $theme }}-600 text-white hover:bg-{{ $theme }}-700 rounded-xl shadow-lg transition-colors">Apply
                        Discount</button>
                </div>
            </div>
        </div>

        <!-- Right: Payment Interface -->
        <div class="w-2/3 bg-white flex flex-col relative">

            <div class="absolute top-0 right-0 p-6 flex items-center gap-2">
                <div class="flex items-center gap-2 text-sm text-slate-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span x-text="new Date().toLocaleTimeString('en-US', {hour: '2-digit', minute:'2-digit'})"></span>
                </div>
            </div>

            <div class="flex-grow flex flex-col p-8 h-full">

                <!-- Header Stats -->
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 pb-2">
                        <span class="text-slate-500 text-sm font-bold uppercase tracking-wider block mb-1">Paid So
                            Far</span>
                        <span class="text-3xl font-black text-green-600" x-text="formatPrice(totalPaid)"></span>
                    </div>
                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 pb-2">
                        <span class="text-slate-500 text-sm font-bold uppercase tracking-wider block mb-1">Remaining
                            Due</span>
                        <span class="text-3xl font-black text-red-500" x-text="formatPrice(remainingDue)"></span>
                    </div>
                </div>

                <div class="flex gap-6 h-full min-h-0">
                    <!-- Left: Input Controls -->
                    <div class="w-1/2 flex flex-col gap-4">
                        <!-- Method Tabs -->
                        <div class="grid grid-cols-3 gap-2">
                            @foreach(['cash' => 'Cash', 'card' => 'Card', 'qr' => 'QR Pay'] as $key => $label)
                                <button @click="paymentMethod = '{{ $key }}'"
                                    :class="{'ring-2 ring-{{ $theme }}-600 bg-{{ $theme }}-50 text-{{ $theme }}-700 font-bold': paymentMethod === '{{ $key }}', 'bg-white border border-slate-200 text-slate-600 hover:border-{{ $theme }}-300': paymentMethod !== '{{ $key }}'}"
                                    class="p-3 rounded-xl flex items-center justify-center gap-2 transition-all text-sm">
                                    <span>{{ $label }}</span>
                                </button>
                            @endforeach
                        </div>

                        <!-- Amount Display -->
                        <div class="bg-white border-2 border-slate-200 rounded-xl p-4 text-right">
                            <span class="text-slate-400 text-lg mr-1" x-text="currency"></span>
                            <span class="text-4xl font-black text-slate-800" x-text="tenderAmountDisplay || '0'"></span>
                        </div>

                        <!-- Numpad -->
                        <div class="grid grid-cols-4 gap-2 flex-grow">
                            <div class="col-span-3 grid grid-cols-3 gap-2">
                                <template x-for="n in [1,2,3,4,5,6,7,8,9]" :key="n">
                                    <button @click="appendNumber(n)"
                                        class="bg-white border border-slate-200 hover:bg-slate-50 rounded-lg py-3 text-xl font-bold text-slate-700 shadow-sm transition-all active:scale-95">
                                        <span x-text="n"></span>
                                    </button>
                                </template>
                                <button @click="appendNumber('.')"
                                    class="bg-white border border-slate-200 hover:bg-slate-50 rounded-lg py-3 text-xl font-bold text-slate-700 shadow-sm">.</button>
                                <button @click="appendNumber(0)"
                                    class="bg-white border border-slate-200 hover:bg-slate-50 rounded-lg py-3 text-xl font-bold text-slate-700 shadow-sm">0</button>
                                <button @click="backspace()"
                                    class="bg-slate-100 hover:bg-slate-200 rounded-lg py-3 flex items-center justify-center text-slate-600 shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M3 12l6.414 6.414a2 2 0 001.414.586H19a2 2 0 002-2V7a2 2 0 00-2-2h-8.172a2 2 0 00-1.414.586L3 12z">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                            <div class="col-span-1 flex flex-col gap-2">
                                <button @click="setExact()"
                                    class="flex-1 bg-{{ $theme }}-50 hover:bg-{{ $theme }}-100 text-{{ $theme }}-700 font-bold rounded-lg text-xs">Exact</button>
                                <button @click="addAmount(10)"
                                    class="flex-1 bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 font-bold rounded-lg text-xs">+10</button>
                                <button @click="addAmount(50)"
                                    class="flex-1 bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 font-bold rounded-lg text-xs">+50</button>
                            </div>
                        </div>

                        <!-- Add Payment Button -->
                        <button @click="addPayment()" :disabled="tenderAmount <= 0"
                            class="w-full bg-slate-800 hover:bg-slate-900 disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold py-4 rounded-xl shadow-lg flex items-center justify-center gap-2 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add Payment
                        </button>
                    </div>

                    <!-- Right: Payment List -->
                    <div class="w-1/2 flex flex-col bg-slate-50 rounded-2xl border border-slate-200 overflow-hidden">
                        <div
                            class="p-3 bg-slate-100 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wide">
                            Payments Received
                        </div>
                        <div class="flex-grow overflow-y-auto p-4 space-y-3 custom-scrollbar">
                            <template x-for="(payment, index) in payments" :key="index">
                                <div
                                    class="bg-white p-3 rounded-lg border border-slate-100 shadow-sm flex justify-between items-center group">
                                    <div class="flex items-center gap-3">
                                        <div class="p-2 rounded-md bg-slate-100 text-slate-500">
                                            <!-- Icons based on method -->
                                            <svg x-show="payment.method === 'cash'" class="w-5 h-5" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                                                </path>
                                            </svg>
                                            <svg x-show="payment.method === 'card'" class="w-5 h-5" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                                                </path>
                                            </svg>
                                            <svg x-show="payment.method === 'qr'" class="w-5 h-5" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z">
                                                </path>
                                            </svg>
                                        </div>
                                        <span class="font-bold text-slate-700 capitalize"
                                            x-text="payment.method"></span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="font-bold text-slate-800"
                                            x-text="formatPrice(payment.amount)"></span>
                                        <button @click="removePayment(index)"
                                            class="text-slate-300 hover:text-red-500 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </template>
                            <div x-show="payments.length === 0"
                                class="flex flex-col items-center justify-center h-48 text-slate-400">
                                <span class="text-sm italic">No payments added yet</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Action -->
                <div class="mt-6">
                    <button @click="processPayment()" :disabled="remainingDue > 0"
                        :class="{'opacity-50 cursor-not-allowed bg-slate-300 text-slate-500': remainingDue > 0, 'bg-{{ $theme }}-600 hover:bg-{{ $theme }}-700 text-white shadow-{{ $theme }}-200 shadow-xl': remainingDue <= 0}"
                        class="w-full font-bold py-5 rounded-2xl text-xl transform hover:-translate-y-0.5 transition-all flex items-center justify-center gap-3">
                        <span x-text="remainingDue > 0 ? 'Pay Remaining Balance' : 'Complete Order'"></span>
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                    </button>
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
                showDiscountModal: false,
                pinInput: '',
                tempDiscountType: 'amount',
                tempDiscountValue: '',
                tempDiscountReason: '',

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
                    this.taxAmount = this.subtotal * (this.taxRate / 100);
                    // Ensure total doesn't go below 0
                    this.total = Math.max(0, this.subtotal + this.taxAmount - this.discountAmount);
                },

                // Discount Logic
                openPinModal() {
                    this.pinInput = '';
                    this.showPinModal = true;
                },

                async verifyPin() {
                    if (!this.pinInput) return;

                    try {
                        const response = await fetch('{{ route('api.pos.verify-pin') }}', {
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
                            Swal.fire({ icon: 'error', title: 'Invalid PIN', timer: 1500, showConfirmButton: false });
                        }
                    } catch (e) {
                        console.error(e);
                        Swal.fire({ icon: 'error', title: 'Error verifying PIN' });
                    }
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
                    this.calculateTotals();
                    this.payments = [];
                    this.tenderAmount = this.total;
                    this.tenderAmountDisplay = this.total.toFixed(2);
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
                        heightAuto: false
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
                                width: 400,
                                heightAuto: false
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