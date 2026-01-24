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
                        class="px-2.5 py-0.5 bg-indigo-50 text-indigo-700 text-xs font-bold rounded-md tracking-wide">#<span
                            x-text="orderId"></span></span>
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
                </div>
                <div class="flex justify-between items-end">
                    <span class="text-slate-800 font-bold text-lg">Total Payable</span>
                    <span class="text-3xl font-black text-indigo-600 tracking-tight" x-text="formatPrice(total)"></span>
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

            <div class="flex-grow flex flex-col justify-center max-w-xl mx-auto w-full px-8">

                <!-- Payment Method Tabs -->
                <div class="grid grid-cols-3 gap-3 mb-8">
                    <button @click="paymentMethod = 'cash'"
                        :class="{'ring-2 ring-indigo-600 bg-indigo-50 text-indigo-700': paymentMethod === 'cash', 'bg-white border border-slate-200 text-slate-600 hover:border-indigo-300': paymentMethod !== 'cash'}"
                        class="p-4 rounded-xl flex flex-col items-center gap-2 transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                        <span class="font-bold">Cash</span>
                    </button>
                    <button @click="paymentMethod = 'card'"
                        :class="{'ring-2 ring-indigo-600 bg-indigo-50 text-indigo-700': paymentMethod === 'card', 'bg-white border border-slate-200 text-slate-600 hover:border-indigo-300': paymentMethod !== 'card'}"
                        class="p-4 rounded-xl flex flex-col items-center gap-2 transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                            </path>
                        </svg>
                        <span class="font-bold">Card</span>
                    </button>
                    <button @click="paymentMethod = 'qr'"
                        :class="{'ring-2 ring-indigo-600 bg-indigo-50 text-indigo-700': paymentMethod === 'qr', 'bg-white border border-slate-200 text-slate-600 hover:border-indigo-300': paymentMethod !== 'qr'}"
                        class="p-4 rounded-xl flex flex-col items-center gap-2 transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z">
                            </path>
                        </svg>
                        <span class="font-bold">QR Pay</span>
                    </button>
                </div>

                <!-- Cash Interface -->
                <div x-show="paymentMethod === 'cash'">
                    <div class="bg-indigo-50 rounded-2xl p-6 mb-6">
                        <div class="flex justify-between mb-2">
                            <span class="text-slate-500 font-medium">Cash Received</span>
                            <span class="text-slate-500 font-medium">Change</span>
                        </div>
                        <div class="flex justify-between items-end">
                            <div class="flex items-center text-4xl font-extrabold text-slate-800">
                                <span class="text-2xl text-slate-400 mr-1" x-text="currency"></span>
                                <span x-text="tenderAmountDisplay || '0'"></span>
                            </div>
                            <div class="text-right">
                                <span class="text-3xl font-bold"
                                    :class="changeAmount >= 0 ? 'text-green-600' : 'text-slate-300'"
                                    x-text="formatPrice(Math.max(0, changeAmount))"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Keypad & Quick Amounts -->
                    <div class="grid grid-cols-4 gap-4">
                        <div class="col-span-3 grid grid-cols-3 gap-3">
                            <template x-for="n in [1,2,3,4,5,6,7,8,9]" :key="n">
                                <button @click="appendNumber(n)"
                                    class="bg-white border border-slate-200 hover:bg-slate-50 hover:border-slate-300 rounded-xl py-4 text-xl font-bold text-slate-700 transition-colors shadow-sm">
                                    <span x-text="n"></span>
                                </button>
                            </template>
                            <button @click="appendNumber('.')"
                                class="bg-white border border-slate-200 hover:bg-slate-50 hover:border-slate-300 rounded-xl py-4 text-xl font-bold text-slate-700 transition-colors shadow-sm">.</button>
                            <button @click="appendNumber(0)"
                                class="bg-white border border-slate-200 hover:bg-slate-50 hover:border-slate-300 rounded-xl py-4 text-xl font-bold text-slate-700 transition-colors shadow-sm">0</button>
                            <button @click="backspace()"
                                class="bg-slate-100 hover:bg-slate-200 rounded-xl py-4 flex items-center justify-center text-slate-600 transition-colors shadow-sm">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M3 12l6.414 6.414a2 2 0 001.414.586H19a2 2 0 002-2V7a2 2 0 00-2-2h-8.172a2 2 0 00-1.414.586L3 12z">
                                    </path>
                                </svg>
                            </button>
                        </div>
                        <div class="col-span-1 space-y-3">
                            <button @click="setExact()"
                                class="w-full bg-indigo-100 hover:bg-indigo-200 text-indigo-700 font-bold py-4 rounded-xl text-sm transition-colors">Exact</button>
                            <button @click="addAmount(10)"
                                class="w-full bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 font-bold py-4 rounded-xl text-sm transition-colors shadow-sm">+10</button>
                            <button @click="addAmount(50)"
                                class="w-full bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 font-bold py-4 rounded-xl text-sm transition-colors shadow-sm">+50</button>
                            <button @click="addAmount(100)"
                                class="w-full bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 font-bold py-4 rounded-xl text-sm transition-colors shadow-sm">+100</button>
                        </div>
                    </div>
                </div>

                <!-- Other Payment Methods Placeholder -->
                <div x-show="paymentMethod !== 'cash'"
                    class="min-h-[300px] flex flex-col items-center justify-center text-slate-400 border-2 border-dashed border-slate-200 rounded-2xl bg-slate-50">
                    <svg class="w-16 h-16 mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="font-medium">Selected Payment Integration would go here</p>
                    <p class="text-sm mt-2">Simulate successful payment for now.</p>
                </div>

            </div>

            <div class="p-8 border-t border-slate-100 bg-white">
                <button @click="processPayment()" :disabled="paymentMethod === 'cash' && changeAmount < 0"
                    :class="{'opacity-50 cursor-not-allowed': paymentMethod === 'cash' && changeAmount < 0}"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-5 rounded-2xl shadow-xl shadow-indigo-200 text-xl transform hover:-translate-y-0.5 transition-all flex items-center justify-center gap-3">
                    <span>Complete Order</span>
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </button>
            </div>
        </div>

    </div>

    <script>
        function checkoutApp() {
            return {
                cart: [],
                currency: '{{ $outletSettings['currency_symbol'] ?? '$' }}',
                taxRate: {{ $outletSettings['tax_rate'] ?? 0 }},
                subtotal: 0,
                taxAmount: 0,
                total: 0,
                orderId: Math.floor(1000 + Math.random() * 9000),

                paymentMethod: 'cash',
                tenderAmount: 0,
                tenderAmountDisplay: '', // String for input

                init() {
                    const storedCart = localStorage.getItem('pos_cart');
                    if (storedCart) {
                        this.cart = JSON.parse(storedCart);
                        this.calculateTotals();
                    } else {
                        // Redirect back if empty (optional safety)
                        // window.location.href = '{{ route('pos.home') }}';
                    }
                },

                calculateTotals() {
                    this.subtotal = this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                    this.taxAmount = this.subtotal * (this.taxRate / 100);
                    this.total = this.subtotal + this.taxAmount;
                },

                get changeAmount() {
                    return this.tenderAmount - this.total;
                },

                formatPrice(amount) {
                    return this.currency + parseFloat(amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                },

                // Keypad Logic
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
                    this.tenderAmount = this.total;
                    this.tenderAmountDisplay = this.total.toFixed(2);
                },

                addAmount(amount) {
                    this.tenderAmount += amount;
                    this.tenderAmountDisplay = this.tenderAmount.toFixed(2); // Or keep normal?
                },

                processPayment() {
                    // 1. Processing State
                    Swal.fire({
                        html: `
                            <div class="py-6">
                                <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-indigo-100 border-t-indigo-600 mb-4"></div>
                                <h3 class="text-xl font-bold text-slate-800">Processing Payment</h3>
                                <p class="text-sm text-slate-500 mt-2">Please wait while we secure the transaction...</p>
                            </div>
                        `,
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        width: 400,
                        padding: '2rem',
                        customClass: {
                            popup: 'rounded-[24px] shadow-2xl'
                        },
                        heightAuto: false,
                        timer: 2000, // Simulate delay
                    }).then(() => {
                        // 2. Success State
                        Swal.fire({
                            html: `
                                <div class="text-center py-4">
                                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <h3 class="text-2xl font-black text-slate-800 mb-2">Payment Successful!</h3>
                                    <p class="text-slate-500">Order #${this.orderId} has been verified.</p>
                                    
                                    <div class="bg-slate-50 rounded-xl p-4 mt-6 mb-2 border border-slate-100">
                                        <div class="flex justify-between items-center text-sm text-slate-600 mb-2">
                                            <span>Amount Paid</span>
                                            <span class="font-bold">${this.tenderAmountDisplay ? this.formatPrice(this.tenderAmount) : this.formatPrice(this.total)}</span>
                                        </div>
                                        <div class="flex justify-between items-center text-lg text-slate-800 font-bold border-t border-dashed border-slate-200 pt-2">
                                            <span>Change Due</span>
                                            <span class="text-indigo-600">${this.formatPrice(Math.max(0, this.changeAmount))}</span>
                                        </div>
                                    </div>
                                </div>
                            `,
                            showConfirmButton: true,
                            confirmButtonText: 'Start New Order',
                            heightAuto: false,
                            width: 450,
                            padding: '2.5rem',
                            customClass: {
                                popup: 'rounded-[32px] shadow-2xl',
                                confirmButton: 'w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-xl text-lg shadow-lg shadow-indigo-200 transition-all transform hover:scale-[1.02]'
                            }
                        }).then(() => {
                            localStorage.removeItem('pos_cart');
                            window.location.href = '{{ route('pos.home') }}';
                        });
                    });
                }
            }
        }
    </script>
</body>

</html>