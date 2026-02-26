@extends('layouts.admin')

@section('title', 'My Profile')
@section('header', 'My Profile')

@section('content')
    <div class="max-w-3xl mx-auto space-y-6">

        {{-- Flash messages --}}
        @if(session('success'))
            <div
                class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl px-4 py-3 text-sm">
                <i class="hgi-stroke hgi-checkmark-circle-01 text-lg shrink-0"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- Profile Header Card --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="h-20 bg-gradient-to-r from-indigo-600 to-violet-600"></div>
            <div class="px-6 pb-6 -mt-8 flex items-end gap-4">
                <div
                    class="w-16 h-16 rounded-2xl bg-white border-4 border-white shadow-md flex items-center justify-center">
                    <span class="text-2xl font-black text-indigo-600">{{ substr(Auth::user()->name, 0, 1) }}</span>
                </div>
                <div class="pb-1 flex-1 min-w-0">
                    <h2 class="text-xl font-bold text-gray-900 truncate">{{ Auth::user()->name }}</h2>
                    <div class="flex items-center gap-2 flex-wrap mt-1">
                        <span
                            class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-indigo-100 text-indigo-700 capitalize">
                            {{ Auth::user()->role ?? 'Admin' }}
                        </span>
                        @if(Auth::user()->outlet)
                            <span
                                class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 flex items-center gap-1">
                                <i class="hgi-stroke hgi-store-01 text-xs"></i> {{ Auth::user()->outlet->name }}
                            </span>
                        @endif
                        @if(Auth::user()->staff_id)
                            <span class="text-xs text-gray-400">ID: {{ Auth::user()->staff_id }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Edit Info Card --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-base font-semibold text-gray-800 mb-5 flex items-center gap-2">
                <i class="hgi-stroke hgi-user-edit-01 text-indigo-600 text-lg"></i> Personal Information
            </h3>

            @if($errors->hasAny(['name', 'email', 'phone']))
                <div
                    class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-600 rounded-xl px-4 py-3 text-sm mb-4">
                    <i class="hgi-stroke hgi-alert-circle text-lg shrink-0 mt-0.5"></i>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach(['name', 'email', 'phone'] as $f)
                            @error($f) <li>{{ $message }}</li> @enderror
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.profile.update') }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Full Name</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                            class="w-full border border-gray-300 rounded-xl px-3.5 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all placeholder-gray-400">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Email Address</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                            class="w-full border border-gray-300 rounded-xl px-3.5 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all placeholder-gray-400">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Phone Number</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone ?? '') }}"
                            placeholder="e.g. +60 12-345 6789"
                            class="w-full border border-gray-300 rounded-xl px-3.5 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all placeholder-gray-400">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Staff ID</label>
                        <input type="text" value="{{ $user->staff_id }}" disabled
                            class="w-full border border-gray-200 bg-gray-50 rounded-xl px-3.5 py-2.5 text-sm text-gray-400 cursor-not-allowed">
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit"
                        class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl shadow-sm hover:shadow-indigo-200 transition-all hover:-translate-y-0.5 active:translate-y-0">
                        <i class="hgi-stroke hgi-floppy-disk text-sm"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>

        {{-- Change Password Card --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-base font-semibold text-gray-800 mb-5 flex items-center gap-2">
                <i class="hgi-stroke hgi-lock-01 text-indigo-600 text-lg"></i> Change Password
            </h3>

            @if($errors->hasAny(['current_password', 'password']))
                <div
                    class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-600 rounded-xl px-4 py-3 text-sm mb-4">
                    <i class="hgi-stroke hgi-alert-circle text-lg shrink-0 mt-0.5"></i>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach(['current_password', 'password'] as $f)
                            @error($f) <li>{{ $message }}</li> @enderror
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.profile.password') }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Current Password</label>
                    <input type="password" name="current_password" required autocomplete="current-password"
                        class="w-full border border-gray-300 rounded-xl px-3.5 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">New Password</label>
                        <input type="password" name="password" required autocomplete="new-password"
                            class="w-full border border-gray-300 rounded-xl px-3.5 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Confirm New Password</label>
                        <input type="password" name="password_confirmation" required autocomplete="new-password"
                            class="w-full border border-gray-300 rounded-xl px-3.5 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                    </div>
                </div>
                <div class="flex justify-end pt-2">
                    <button type="submit"
                        class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl shadow-sm hover:shadow-red-200 transition-all hover:-translate-y-0.5 active:translate-y-0">
                        <i class="hgi-stroke hgi-lock-password text-sm"></i> Update Password
                    </button>
                </div>
            </form>
        </div>

    </div>
@endsection