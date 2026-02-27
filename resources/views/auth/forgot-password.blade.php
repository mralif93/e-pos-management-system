@extends('layouts.guest')

@section('title', 'Forgot Password')

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
                    <i class="hgi-stroke text-[20px] hgi-user-unlock-01 text-white text-4xl"></i>
                </div>
                <h1 class="text-md font-bold text-white">Forgot Password?</h1>
                <p class="text-indigo-100 mt-1">Enter your email to reset password</p>
            </div>

            <div class="p-8">
                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl flex items-center gap-3">
                        <i class="hgi-stroke text-[20px] hgi-tick-circle text-green-600"></i>
                        <p class="text-green-700 text-sm">{{ session('success') }}</p>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl flex items-center gap-3">
                        <i class="hgi-stroke text-[20px] hgi-alert-01 text-red-600"></i>
                        <p class="text-red-700 text-sm">{{ session('error') }}</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
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

                        <button type="submit"
                            class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold py-4 px-4 rounded-xl transition-all duration-200 transform hover:scale-[1.02] shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                            <i class="hgi-stroke text-[20px] hgi-sent"></i> Send Reset Link
                        </button>

                        <div class="text-center">
                            <a href="{{ route('login') }}"
                                class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium inline-flex items-center gap-1">
                                <i class="hgi-stroke text-[20px] hgi-arrow-left-01 text-sm"></i> Back to Login
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <div
                class="bg-gray-50 dark:bg-secondary-800/80 px-8 py-4 text-center border-t border-gray-100 dark:border-secondary-700/80">
                <p class="text-sm text-gray-500 dark:text-secondary-400 flex items-center justify-center gap-2">
                    <i class="hgi-stroke text-[20px] hgi-shield-01 text-green-500 text-sm"></i> Secure Password Reset
                </p>
            </div>
        </div>

        </div>
    </main>
@endsection