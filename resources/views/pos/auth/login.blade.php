<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - E-POS</title>
    <!-- Tailwind CSS (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('assets/fonts/inter.css') }}" />
    <!-- HugeIcons -->
    <link rel="stylesheet" href="{{ asset('assets/icons/hgi-stroke-rounded.css') }}" />
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body
    class="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-600 via-indigo-700 to-purple-800 p-4">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-10 text-center">
            <div class="w-24 h-24 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse">
                <i class="hgi-stroke text-[20px] hgi-computer text-white text-5xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-white">E-POS</h1>
            <p class="text-indigo-100 mt-2 font-medium">Point of Sale System</p>
        </div>

        <div class="p-8">
            <form method="POST" action="{{ url('/pos/login') }}" id="login-form">
                @csrf

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="hgi-stroke text-[20px] hgi-mail-01 text-gray-400"></i>
                            </div>
                            <input type="email" name="email" id="email" placeholder="Enter your email" required
                                autofocus
                                class="w-full pl-12 pr-4 py-3.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all shadow-sm">
                        </div>
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="hgi-stroke text-[20px] hgi-lock-password text-gray-400"></i>
                            </div>
                            <input type="password" name="password" id="password" placeholder="Enter your password"
                                required
                                class="w-full pl-12 pr-14 py-3.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all shadow-sm">
                            <button type="button" onclick="togglePassword()"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center">
                                <span id="toggle-icon"
                                    class="hgi-stroke text-[20px] hgi-view text-gray-400 hover:text-indigo-600 transition-colors text-lg"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="remember" id="remember"
                                class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-600">Remember me</span>
                        </label>
                    </div>

                    <button type="submit"
                        class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold py-4 px-4 rounded-xl transition-all duration-200 transform hover:scale-[1.02] shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                        <i class="hgi-stroke text-[20px] hgi-login-01"></i> Sign In
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-gray-50 px-8 py-4 text-center border-t border-gray-100">
            <p class="text-sm text-gray-500 flex items-center justify-center gap-2">
                <i class="hgi-stroke text-[20px] hgi-user-check-01 text-green-500 text-sm"></i> Secure Access
            </p>
        </div>
        <!-- SweetAlert2 -->
        <script src="{{ asset('assets/js/sweetalert2.js') }}"></script>
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
</body>

</html>