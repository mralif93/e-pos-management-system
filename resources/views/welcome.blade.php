<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'e-POS') }} - Modern POS for Malaysian Businesses</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>

<body
    class="font-sans antialiased bg-gradient-to-br from-secondary-50 via-white to-primary-50 dark:from-secondary-900 dark:via-secondary-900 dark:to-secondary-800 text-secondary-900 dark:text-secondary-100">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header
            class="bg-white/80 dark:bg-secondary-800/80 backdrop-blur-md border-b border-secondary-200 dark:border-secondary-700 sticky top-0 z-50 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 flex items-center gap-2">
                            <div class="bg-gradient-to-br from-primary-600 to-primary-700 p-2 rounded-xl shadow-lg">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <span
                                class="text-2xl font-bold bg-gradient-to-r from-primary-600 to-primary-700 dark:from-primary-400 dark:to-primary-500 bg-clip-text text-transparent">e-POS</span>
                        </div>
                        <span
                            class="text-xs font-semibold bg-gradient-to-r from-lhdn-green to-success-600 text-white px-3 py-1 rounded-full shadow-md">LHDN
                            Verified</span>
                        <nav class="hidden md:ml-6 md:flex md:space-x-6">
                            <a href="#features"
                                class="text-secondary-600 hover:text-primary-600 dark:text-secondary-300 dark:hover:text-primary-400 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-primary-50 dark:hover:bg-secondary-700">Features</a>
                            <a href="#compliance"
                                class="text-secondary-600 hover:text-primary-600 dark:text-secondary-300 dark:hover:text-primary-400 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-primary-50 dark:hover:bg-secondary-700">Compliance</a>
                        </nav>
                    </div>
                    <div class="flex items-center space-x-3">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}"
                                    class="text-secondary-700 hover:text-primary-600 dark:text-secondary-200 dark:hover:text-primary-400 font-medium transition-colors">Dashboard</a>
                            @else
                                <a href="{{ route('login') }}"
                                    class="text-secondary-700 hover:text-primary-600 dark:text-secondary-200 dark:hover:text-primary-400 font-medium transition-colors hidden sm:block">Log
                                    in</a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}"
                                        class="bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white px-5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">Get
                                        Started</a>
                                @endif
                            @endauth
                        @endif
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-grow">
            <!-- Hero Section -->
            <div class="relative overflow-hidden">
                <!-- Background Decorations -->
                <div class="absolute inset-0 overflow-hidden pointer-events-none">
                    <div class="absolute top-0 right-0 w-96 h-96 bg-primary-300/20 rounded-full blur-3xl"></div>
                    <div class="absolute bottom-0 left-0 w-96 h-96 bg-success-300/20 rounded-full blur-3xl"></div>
                </div>

                <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center py-16 sm:py-20 lg:py-28">
                        <!-- Left Column - Content -->
                        <div class="text-center lg:text-left">
                            <div class="inline-block mb-4">
                                <span
                                    class="bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 px-4 py-2 rounded-full text-sm font-semibold">
                                    🇲🇾 Made for Malaysian SMEs
                                </span>
                            </div>
                            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight mb-6">
                                <span class="block text-secondary-900 dark:text-white">Modern POS for</span>
                                <span
                                    class="block bg-gradient-to-r from-primary-600 via-primary-500 to-success-600 dark:from-primary-400 dark:via-primary-300 dark:to-success-400 bg-clip-text text-transparent">Malaysian
                                    Businesses</span>
                            </h1>
                            <p
                                class="mt-6 text-lg sm:text-xl text-secondary-600 dark:text-secondary-400 max-w-2xl mx-auto lg:mx-0 leading-relaxed">
                                Fully compliant with <span
                                    class="font-semibold text-lhdn-green dark:text-success-400">LHDN e-Invoice</span>,
                                PDPA, and SST. Streamline your retail or F&B operations with our offline-capable,
                                cloud-synced point of sale system.
                            </p>
                            <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                                <a href="#"
                                    class="group inline-flex items-center justify-center px-8 py-4 text-base font-semibold rounded-xl text-white bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-200">
                                    Start Free Trial
                                    <svg class="ml-2 h-5 w-5 group-hover:translate-x-1 transition-transform" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                    </svg>
                                </a>
                                <a href="#features"
                                    class="inline-flex items-center justify-center px-8 py-4 text-base font-semibold rounded-xl text-primary-700 dark:text-primary-300 bg-primary-100 dark:bg-primary-900/30 hover:bg-primary-200 dark:hover:bg-primary-900/50 border border-primary-200 dark:border-primary-800 transition-all duration-200">
                                    View Features
                                </a>
                            </div>
                            <!-- Trust Indicators -->
                            <div
                                class="mt-12 flex flex-wrap items-center justify-center lg:justify-start gap-6 text-sm text-secondary-600 dark:text-secondary-400">
                                <div class="flex items-center gap-2">
                                    <svg class="h-5 w-5 text-success-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span>LHDN Compliant</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="h-5 w-5 text-success-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span>Offline Capable</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="h-5 w-5 text-success-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span>Multi-Outlet</span>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column - Visual -->
                        <div class="relative">
                            <div
                                class="relative bg-gradient-to-br from-primary-100 to-success-100 dark:from-primary-900/20 dark:to-success-900/20 rounded-2xl p-8 shadow-2xl border border-primary-200 dark:border-primary-800">
                                <div class="aspect-square flex items-center justify-center">
                                    <div class="text-center">
                                        <div
                                            class="inline-flex items-center justify-center h-40 w-40 rounded-full bg-gradient-to-br from-primary-500 to-primary-700 text-white mb-6 shadow-xl">
                                            <svg class="h-20 w-20" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                        </div>
                                        <h3 class="text-2xl font-bold text-secondary-900 dark:text-white mb-2">POS
                                            Dashboard</h3>
                                        <p class="text-secondary-600 dark:text-secondary-400">Preview coming soon</p>
                                    </div>
                                </div>
                                <!-- Floating Elements -->
                                <div
                                    class="absolute -top-4 -right-4 bg-white dark:bg-secondary-800 rounded-xl shadow-lg px-4 py-2 border border-secondary-200 dark:border-secondary-700">
                                    <p class="text-xs font-semibold text-secondary-600 dark:text-secondary-400">
                                        Real-time Sync</p>
                                </div>
                                <div
                                    class="absolute -bottom-4 -left-4 bg-white dark:bg-secondary-800 rounded-xl shadow-lg px-4 py-2 border border-secondary-200 dark:border-secondary-700">
                                    <p class="text-xs font-semibold text-lhdn-green dark:text-success-400">✓ LHDN
                                        Verified</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Features Grid -->
            <div id="features" class="py-20 bg-white dark:bg-secondary-900">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center max-w-3xl mx-auto mb-16">
                        <h2
                            class="text-sm font-bold tracking-wide uppercase text-primary-600 dark:text-primary-400 mb-3">
                            FEATURES</h2>
                        <p
                            class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-secondary-900 dark:text-white mb-4">
                            Everything you need to run your business
                        </p>
                        <p class="text-lg sm:text-xl text-secondary-600 dark:text-secondary-400">
                            From inventory management to tax compliance, we have got you covered.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <!-- Feature 1: Core POS -->
                        <div
                            class="group relative bg-gradient-to-br from-white to-secondary-50 dark:from-secondary-800 dark:to-secondary-900 rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 border border-secondary-200 dark:border-secondary-700 hover:border-primary-300 dark:hover:border-primary-700 transform hover:-translate-y-2">
                            <div
                                class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-primary-500/10 to-transparent rounded-bl-full">
                            </div>
                            <div class="relative">
                                <div
                                    class="flex items-center justify-center h-14 w-14 rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 text-white mb-5 shadow-lg group-hover:scale-110 transition-transform duration-300">
                                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold text-secondary-900 dark:text-white mb-3">Smart Point of
                                    Sale</h3>
                                <p class="text-base text-secondary-600 dark:text-secondary-400 leading-relaxed">
                                    Real-time cart, barcode scanning, and support for multiple payment methods including
                                    DuitNow QR and split payments.
                                </p>
                            </div>
                        </div>

                        <!-- Feature 2: LHDN e-Invoice -->
                        <div
                            class="group relative bg-gradient-to-br from-white to-secondary-50 dark:from-secondary-800 dark:to-secondary-900 rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 border border-secondary-200 dark:border-secondary-700 hover:border-lhdn-green dark:hover:border-success-700 transform hover:-translate-y-2">
                            <div
                                class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-lhdn-green/10 to-transparent rounded-bl-full">
                            </div>
                            <div class="relative">
                                <div
                                    class="flex items-center justify-center h-14 w-14 rounded-xl bg-gradient-to-br from-lhdn-green to-success-700 text-white mb-5 shadow-lg group-hover:scale-110 transition-transform duration-300">
                                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold text-secondary-900 dark:text-white mb-3">LHDN e-Invoice
                                    Ready</h3>
                                <p class="text-base text-secondary-600 dark:text-secondary-400 leading-relaxed">
                                    Automatic XML generation, MyInvois API integration, and QR code verification links
                                    on receipts.
                                </p>
                            </div>
                        </div>

                        <!-- Feature 3: Offline Mode -->
                        <div
                            class="group relative bg-gradient-to-br from-white to-secondary-50 dark:from-secondary-800 dark:to-secondary-900 rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 border border-secondary-200 dark:border-secondary-700 hover:border-primary-300 dark:hover:border-primary-700 transform hover:-translate-y-2">
                            <div
                                class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-primary-500/10 to-transparent rounded-bl-full">
                            </div>
                            <div class="relative">
                                <div
                                    class="flex items-center justify-center h-14 w-14 rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 text-white mb-5 shadow-lg group-hover:scale-110 transition-transform duration-300">
                                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold text-secondary-900 dark:text-white mb-3">Works Offline</h3>
                                <p class="text-base text-secondary-600 dark:text-secondary-400 leading-relaxed">
                                    Continue selling even when the internet is down. Auto-syncs data securely once
                                    connection is restored.
                                </p>
                            </div>
                        </div>

                        <!-- Feature 4: Product Management -->
                        <div
                            class="group relative bg-gradient-to-br from-white to-secondary-50 dark:from-secondary-800 dark:to-secondary-900 rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 border border-secondary-200 dark:border-secondary-700 hover:border-primary-300 dark:hover:border-primary-700 transform hover:-translate-y-2">
                            <div
                                class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-primary-500/10 to-transparent rounded-bl-full">
                            </div>
                            <div class="relative">
                                <div
                                    class="flex items-center justify-center h-14 w-14 rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 text-white mb-5 shadow-lg group-hover:scale-110 transition-transform duration-300">
                                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold text-secondary-900 dark:text-white mb-3">Inventory Control
                                </h3>
                                <p class="text-base text-secondary-600 dark:text-secondary-400 leading-relaxed">
                                    Manage variants (SKUs), track stock levels, set low stock alerts, and handle
                                    outlet-specific pricing.
                                </p>
                            </div>
                        </div>

                        <!-- Feature 5: Multi-Outlet -->
                        <div
                            class="group relative bg-gradient-to-br from-white to-secondary-50 dark:from-secondary-800 dark:to-secondary-900 rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 border border-secondary-200 dark:border-secondary-700 hover:border-primary-300 dark:hover:border-primary-700 transform hover:-translate-y-2">
                            <div
                                class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-primary-500/10 to-transparent rounded-bl-full">
                            </div>
                            <div class="relative">
                                <div
                                    class="flex items-center justify-center h-14 w-14 rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 text-white mb-5 shadow-lg group-hover:scale-110 transition-transform duration-300">
                                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold text-secondary-900 dark:text-white mb-3">Multi-Outlet
                                    Support</h3>
                                <p class="text-base text-secondary-600 dark:text-secondary-400 leading-relaxed">
                                    Scale your business with centralized management for multiple branches, each with
                                    unique reports and users.
                                </p>
                            </div>
                        </div>

                        <!-- Feature 6: Reporting -->
                        <div
                            class="group relative bg-gradient-to-br from-white to-secondary-50 dark:from-secondary-800 dark:to-secondary-900 rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 border border-secondary-200 dark:border-secondary-700 hover:border-primary-300 dark:hover:border-primary-700 transform hover:-translate-y-2">
                            <div
                                class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-primary-500/10 to-transparent rounded-bl-full">
                            </div>
                            <div class="relative">
                                <div
                                    class="flex items-center justify-center h-14 w-14 rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 text-white mb-5 shadow-lg group-hover:scale-110 transition-transform duration-300">
                                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold text-secondary-900 dark:text-white mb-3">Actionable
                                    Analytics</h3>
                                <p class="text-base text-secondary-600 dark:text-secondary-400 leading-relaxed">
                                    Daily sales summaries, top-selling products, and tax collected reports exported to
                                    Excel or PDF.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CTA Section -->
            <div id="compliance"
                class="relative py-20 bg-gradient-to-br from-primary-600 to-primary-800 dark:from-primary-700 dark:to-primary-900">
                <div class="absolute inset-0 bg-grid-pattern opacity-10"></div>
                <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-white mb-6">
                        Ready to modernize your business?
                    </h2>
                    <p class="text-lg sm:text-xl text-primary-100 mb-8 max-w-2xl mx-auto">
                        Join hundreds of Malaysian businesses already using e-POS to streamline operations and stay
                        compliant.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="#"
                            class="inline-flex items-center justify-center px-8 py-4 text-base font-semibold rounded-xl text-primary-700 bg-white hover:bg-secondary-50 shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-200">
                            Start Free Trial
                            <svg class="ml-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </a>
                        <a href="#"
                            class="inline-flex items-center justify-center px-8 py-4 text-base font-semibold rounded-xl text-white border-2 border-white hover:bg-white/10 transition-all duration-200">
                            Contact Sales
                        </a>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <footer class="bg-secondary-900 dark:bg-black border-t border-secondary-800">
                <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                        <div>
                            <div class="flex items-center gap-2 mb-4">
                                <div class="bg-gradient-to-br from-primary-600 to-primary-700 p-2 rounded-lg">
                                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <span class="text-xl font-bold text-white">e-POS</span>
                            </div>
                            <p class="text-secondary-400 text-sm">
                                Modern point of sale system built for Malaysian businesses.
                            </p>
                        </div>
                        <div>
                            <h3 class="text-white font-semibold mb-3">Product</h3>
                            <ul class="space-y-2 text-sm text-secondary-400">
                                <li><a href="#features" class="hover:text-primary-400 transition-colors">Features</a>
                                </li>
                                <li><a href="#" class="hover:text-primary-400 transition-colors">Pricing</a></li>
                                <li><a href="#" class="hover:text-primary-400 transition-colors">Documentation</a></li>
                            </ul>
                        </div>
                        <div>
                            <h3 class="text-white font-semibold mb-3">Company</h3>
                            <ul class="space-y-2 text-sm text-secondary-400">
                                <li><a href="#" class="hover:text-primary-400 transition-colors">About</a></li>
                                <li><a href="#" class="hover:text-primary-400 transition-colors">Contact</a></li>
                                <li><a href="#" class="hover:text-primary-400 transition-colors">Privacy Policy</a></li>
                            </ul>
                        </div>
                    </div>
                    <div
                        class="border-t border-secondary-800 pt-8 flex flex-col md:flex-row justify-between items-center">
                        <p class="text-secondary-400 text-sm text-center md:text-left">
                            © 2026 e-POS System. All rights reserved. Built with Laravel.
                        </p>
                        <div class="mt-4 md:mt-0 flex items-center gap-2">
                            <span class="text-xs bg-lhdn-green text-white px-3 py-1 rounded-full font-semibold">LHDN
                                Compliant</span>
                            <span
                                class="text-xs bg-success-600 text-white px-3 py-1 rounded-full font-semibold">Malaysian
                                Standard</span>
                        </div>
                    </div>
                </div>
            </footer>
        </main>
    </div>
</body>

</html>