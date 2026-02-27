<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'E-POS'))</title>

    <!-- Tailwind CSS (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Fonts -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/figtree.css') }}" />

    <!-- HugeIcons -->
    <link rel="stylesheet" href="{{ asset('assets/icons/hgi-stroke-rounded.css') }}" />

    <script>
        // On page load or when changing themes, best to add inline in `head` to avoid FOUC
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
    {{-- sweetalert --}}
    <script src="{{ asset('assets/js/sweetalert2.js') }}"></script>
    @yield('styles')
</head>

<body class="font-sans text-secondary-800 antialiased bg-secondary-50 dark:bg-secondary-900 min-h-screen flex flex-col">
    <!-- Header inherited by all guest pages -->
    <header
        class="sticky top-0 z-50 bg-white/80 dark:bg-secondary-900/80 backdrop-blur-md border-b border-secondary-200 dark:border-secondary-800 shadow-sm relative w-full">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex items-center gap-3">
                    <a href="/" class="flex-shrink-0 flex items-center gap-2">
                        <div class="bg-gradient-to-br from-primary-600 to-primary-700 p-2 rounded-xl shadow-lg">
                            <i class="hgi-stroke text-[20px] hgi-store-01 text-white text-3xl"></i>
                        </div>
                        <span class="text-md font-bold text-secondary-800 dark:text-white">e-POS</span>
                    </a>
                </div>
                <!-- Navigation Links for Landing Page (hidden on auth pages if desired, but fine for all) -->
                @if(request()->routeIs('landing') || request()->path() === '/')
                    <nav class="hidden md:flex items-center space-x-8">
                        <a href="#features"
                            class="text-secondary-600 hover:text-primary-600 dark:text-secondary-300 dark:hover:text-primary-400 font-medium transition-colors">Features</a>
                        <a href="#testimonials"
                            class="text-secondary-600 hover:text-primary-600 dark:text-secondary-300 dark:hover:text-primary-400 font-medium transition-colors">Testimonials</a>
                        <a href="#pricing"
                            class="text-secondary-600 hover:text-primary-600 dark:text-secondary-300 dark:hover:text-primary-400 font-medium transition-colors">Pricing</a>
                    </nav>
                @endif
                <div class="flex items-center space-x-3">
                    <!-- Elegant Theme Toggle -->
                    <button id="theme-toggle-btn" type="button"
                        class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-50 hover:bg-gray-100 border border-gray-100 text-gray-500 hover:text-gray-800 transition-all dark:bg-secondary-800 dark:hover:bg-secondary-700 dark:border-secondary-700 dark:text-secondary-400 dark:hover:text-white">
                        <i class="hgi-stroke text-[18px] hgi-sun-01 dark:hidden"></i>
                        <i class="hgi-stroke text-[18px] hgi-moon-01 hidden dark:block"></i>
                    </button>

                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/admin/dashboard') }}"
                                class="inline-flex items-center justify-center px-4 py-2 lg:px-5 lg:py-2.5 text-sm font-semibold rounded-xl text-white bg-primary-600 hover:bg-primary-700 shadow-sm transition-all">
                                Dashboard
                            </a>
                        @else
                            @if(!request()->routeIs('login'))
                                <a href="{{ route('login') }}"
                                    class="inline-flex items-center justify-center px-4 py-2 lg:px-5 lg:py-2.5 text-sm font-semibold rounded-xl text-white bg-primary-600 hover:bg-primary-700 shadow-sm transition-all hidden sm:flex">
                                    Sign In
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </header>

    @yield('content')

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const themeToggleBtn = document.getElementById('theme-toggle-btn');
            if (themeToggleBtn) {
                themeToggleBtn.addEventListener('click', function () {
                    if (localStorage.getItem('color-theme')) {
                        if (localStorage.getItem('color-theme') === 'light') {
                            document.documentElement.classList.add('dark');
                            localStorage.setItem('color-theme', 'dark');
                        } else {
                            document.documentElement.classList.remove('dark');
                            localStorage.setItem('color-theme', 'light');
                        }
                    } else {
                        if (document.documentElement.classList.contains('dark')) {
                            document.documentElement.classList.remove('dark');
                            localStorage.setItem('color-theme', 'light');
                        } else {
                            document.documentElement.classList.add('dark');
                            localStorage.setItem('color-theme', 'dark');
                        }
                    }
                });
            }
        });
    </script>
    @yield('scripts')
</body>

</html>