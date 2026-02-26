<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'E-POS') }} - Reset Password</title>
    <!-- Tailwind CSS (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Fonts -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/figtree.css') }}" />

    <!-- HugeIcons -->
    <link rel="stylesheet" href="{{ asset('assets/icons/hgi-stroke-rounded.css') }}" />
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Figtree', 'sans-serif'] }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Figtree', sans-serif;
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
    </style>
</head>

<body
    class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 p-4">
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-indigo-500/20 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-purple-500/20 rounded-full blur-3xl"></div>
    </div>

    <div class="glass-effect rounded-3xl shadow-2xl w-full max-w-md overflow-hidden relative z-10">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-8 text-center">
            <div class="w-20 h-20 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                <i class="hgi-stroke text-[20px] hgi-lock-password text-white text-4xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-white">Reset Password</h1>
            <p class="text-indigo-100 mt-1">Create your new password</p>
        </div>

        <div class="p-8">
            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl flex items-center gap-3">
                    <i class="hgi-stroke text-[20px] hgi-alert-01 text-red-600"></i>
                    <p class="text-red-700 text-sm">{{ session('error') }}</p>
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="hgi-stroke text-[20px] hgi-mail-01 text-gray-400"></i>
                            </div>
                            <input type="email" name="email" value="{{ $email ?? old('email') }}"
                                placeholder="Enter your email" required readonly
                                class="w-full pl-12 pr-4 py-3.5 border border-gray-200 rounded-xl bg-gray-100 text-gray-500">
                        </div>
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">New Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="hgi-stroke text-[20px] hgi-lock-password text-gray-400"></i>
                            </div>
                            <input type="password" name="password" id="password" placeholder="Enter new password"
                                required
                                class="w-full pl-12 pr-14 py-3.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all bg-gray-50 focus:bg-white">
                            <button type="button" onclick="togglePassword('password')"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center">
                                <span id="toggle-icon-password"
                                    class="hgi-stroke text-[20px] hgi-view text-gray-400 hover:text-indigo-600 transition-colors"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Confirm Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="hgi-stroke text-[20px] hgi-lock-password text-gray-400"></i>
                            </div>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                placeholder="Confirm new password" required
                                class="w-full pl-12 pr-14 py-3.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all bg-gray-50 focus:bg-white">
                            <button type="button" onclick="togglePassword('password_confirmation')"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center">
                                <span id="toggle-icon-password_confirmation"
                                    class="hgi-stroke text-[20px] hgi-view text-gray-400 hover:text-indigo-600 transition-colors"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold py-4 px-4 rounded-xl transition-all duration-200 transform hover:scale-[1.02] shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                        <i class="hgi-stroke text-[20px] hgi-floppy-disk-01"></i> Reset Password
                    </button>

                    <div class="text-center">
                        <a href="{{ route('login') }}"
                            class="text-sm text-indigo-600 hover:text-indigo-800 font-medium inline-flex items-center gap-1">
                            <i class="hgi-stroke text-[20px] hgi-arrow-left-01 text-sm"></i> Back to Login
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="bg-gray-50 px-8 py-4 text-center border-t border-gray-100">
            <p class="text-sm text-gray-500 flex items-center justify-center gap-2">
                <i class="hgi-stroke text-[20px] hgi-shield-01 text-green-500 text-sm"></i> Secure Password Reset
            </p>
        </div>
    </div>
    <!-- SweetAlert2 -->
    <script src="{{ asset('assets/js/sweetalert2.js') }}"></script>
    <script>
        function togglePassword(fieldId) {
            const input = document.getElementById(fieldId);
            const icon = document.getElementById('toggle-icon-' + fieldId);

            if (input.type === 'password') {
                input.type = 'text';
                icon.textContent = 'visibility_off';
            } else {
                input.type = 'password';
                icon.textContent = 'visibility';
            }
        }
    </script>
</body>

</html>