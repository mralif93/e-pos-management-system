@extends('layouts.guest')

@section('title', 'Login')

@section('content')
    <main class="flex-grow flex items-center justify-center p-4 relative overflow-hidden">
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-indigo-500/20 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-purple-500/20 rounded-full blur-3xl"></div>
        </div>

        <div
            class="bg-white/90 dark:bg-secondary-800/90 backdrop-blur-md rounded-3xl shadow-2xl border border-white/20 dark:border-secondary-700/50 w-full max-w-md overflow-hidden relative z-10">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-8 text-center">
                <div class="w-20 h-20 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                    <i class="hgi-stroke text-[20px] hgi-computer text-white text-4xl"></i>
                </div>
                <h1 class="text-md font-bold text-white">E-POS System</h1>
                <p class="text-indigo-100 mt-1">Sign in to continue</p>
            </div>

            <div class="p-8">
                <form method="POST" action="{{ url('/login') }}" id="login-form">
                    @csrf

                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-secondary-300 mb-2">Email
                                Address</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="hgi-stroke text-[20px] hgi-mail-01 text-gray-400 dark:text-secondary-500"></i>
                                </div>
                                <input type="email" name="email" value="{{ old('email') }}" placeholder="Enter your email"
                                    required autofocus
                                    class="w-full pl-12 pr-4 py-3.5 border border-gray-200 dark:border-secondary-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all bg-gray-50 focus:bg-white dark:bg-secondary-900 dark:focus:bg-secondary-800 dark:text-white dark:placeholder-secondary-500">
                            </div>
                            @error('email')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label
                                    class="block text-sm font-semibold text-gray-700 dark:text-secondary-300">Password</label>
                                <a href="{{ route('password.request') }}"
                                    class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium">Forgot
                                    password?</a>
                            </div>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i
                                        class="hgi-stroke text-[20px] hgi-lock-password text-gray-400 dark:text-secondary-500"></i>
                                </div>
                                <input type="password" name="password" id="password" placeholder="Enter your password"
                                    required
                                    class="w-full pl-12 pr-14 py-3.5 border border-gray-200 dark:border-secondary-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all bg-gray-50 focus:bg-white dark:bg-secondary-900 dark:focus:bg-secondary-800 dark:text-white dark:placeholder-secondary-500">
                                <button type="button" onclick="togglePassword()"
                                    class="absolute inset-y-0 right-0 pr-4 flex items-center">
                                    <span id="toggle-icon"
                                        class="hgi-stroke text-[20px] hgi-view text-gray-400 dark:text-secondary-500 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors"></span>
                                </button>
                            </div>
                            @error('password')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="remember"
                                    class="w-5 h-5 text-indigo-600 border-gray-300 dark:border-secondary-600 rounded focus:ring-indigo-500 bg-white dark:bg-secondary-900">
                                <span class="ml-2 text-sm text-gray-600 dark:text-secondary-400">Remember me</span>
                            </label>
                        </div>

                        <button type="submit"
                            class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold py-4 px-4 rounded-xl transition-all duration-200 transform hover:scale-[1.02] shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                            <i class="hgi-stroke text-[20px] hgi-login-01"></i> Sign In
                        </button>
                    </div>
                </form>
            </div>

            <div
                class="bg-gray-50 dark:bg-secondary-800/80 px-8 py-4 text-center border-t border-gray-100 dark:border-secondary-700/80">
                <p class="text-sm text-gray-500 dark:text-secondary-400 flex items-center justify-center gap-2">
                    <i class="hgi-stroke text-[20px] hgi-user-check-01 text-green-500 text-sm"></i> Secure Access
                </p>
            </div>
        </div>
    </main>
@endsection

@section('scripts')
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggle-icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.textContent = 'visibility_off';
            } else {
                passwordInput.type = 'password';
                toggleIcon.textContent = 'visibility';
            }
        }
    </script>

    @if(session('error_popup'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Access Denied',
                    text: "{{ session('error_popup') }}",
                    confirmButtonText: 'OK',
                    customClass: {
                        popup: 'rounded-2xl',
                        confirmButton: 'bg-red-500 text-white px-6 py-2 rounded-lg'
                    }
                });
            });
        </script>
    @endif

    @if($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Login Failed',
                    text: "Invalid email or password",
                    confirmButtonText: 'Try Again',
                    customClass: {
                        popup: 'rounded-2xl',
                        confirmButton: 'bg-indigo-600 text-white px-6 py-2 rounded-lg'
                    }
                });
            });
        </script>
    @endif
@endsection