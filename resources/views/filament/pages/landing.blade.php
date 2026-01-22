<x-filament-panels::page>
    <style>
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .card-animate {
            animation: slideUp 0.5s ease-out;
        }
        
        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>

    <div class="flex items-center justify-center min-h-screen px-4 py-12 -mx-6 -my-6 bg-gray-50 dark:bg-gray-900">
        <div class="w-full max-w-7xl space-y-12">
            
            {{-- Header --}}
            <div class="text-center space-y-6 card-animate">
                {{-- Logo --}}
                <div class="flex justify-center">
                    <div class="flex items-center justify-center w-20 h-20 rounded-2xl shadow-lg gradient-bg">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                </div>
                
                {{-- Title --}}
                <div class="space-y-3">
                    <h1 class="text-6xl font-black gradient-text">Welcome Back!</h1>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $user->name }}</p>
                    <p class="text-lg text-gray-600 dark:text-gray-400 max-w-md mx-auto">Choose your destination to get started with e-POS</p>
                </div>
            </div>

            {{-- Cards --}}
            <div class="grid gap-8 md:grid-cols-2 max-w-6xl mx-auto card-animate" style="animation-delay: 0.1s;">
                
                {{-- Dashboard Card --}}
                <a href="{{ route('filament.admin.pages.dashboard') }}" 
                   class="relative block p-10 overflow-hidden bg-white rounded-3xl shadow-lg dark:bg-gray-800 card-hover group">
                    
                    <div class="space-y-6">
                        {{-- Icon --}}
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl shadow-lg bg-blue-600">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        
                        {{-- Content --}}
                        <div class="space-y-4">
                            <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Dashboard</h2>
                            <p class="text-base leading-relaxed text-gray-600 dark:text-gray-400">
                                Access comprehensive analytics, manage inventory, view detailed reports, and configure system settings
                            </p>
                        </div>
                        
                        {{-- Features --}}
                        <div class="space-y-3">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 mt-0.5 text-blue-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Sales Reports & Analytics</span>
                            </div>
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 mt-0.5 text-blue-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Product & Inventory Management</span>
                            </div>
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 mt-0.5 text-blue-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">System Configuration</span>
                            </div>
                        </div>
                        
                        {{-- CTA --}}
                        <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                            <span class="text-base font-semibold text-blue-600 dark:text-blue-400">Open Dashboard →</span>
                        </div>
                    </div>
                </a>

                {{-- POS Card --}}
                <a href="{{ route('pos.index') }}" 
                   class="relative block p-10 overflow-hidden bg-white rounded-3xl shadow-lg dark:bg-gray-800 card-hover group">
                    
                    <div class="space-y-6">
                        {{-- Icon --}}
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl shadow-lg bg-green-600">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        
                        {{-- Content --}}
                        <div class="space-y-4">
                            <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Point of Sale</h2>
                            <p class="text-base leading-relaxed text-gray-600 dark:text-gray-400">
                                Start selling immediately with our fast POS interface, process payments, and manage customer transactions
                            </p>
                        </div>
                        
                        {{-- Features --}}
                        <div class="space-y-3">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 mt-0.5 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Quick Sales Processing</span>
                            </div>
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 mt-0.5 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Multiple Payment Methods</span>
                            </div>
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 mt-0.5 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Instant Receipt Printing</span>
                            </div>
                        </div>
                        
                        {{-- CTA --}}
                        <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                            <span class="text-base font-semibold text-green-600 dark:text-green-400">Start Selling →</span>
                        </div>
                    </div>
                </a>
            </div>

            {{-- Footer --}}
            <div class="text-center">
                <div class="inline-flex items-center gap-3 px-6 py-3 bg-white rounded-full shadow-md dark:bg-gray-800">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ $user->email }}</span>
                    @if($user->outlet)
                        <span class="text-gray-400">•</span>
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ $user->outlet->name }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>