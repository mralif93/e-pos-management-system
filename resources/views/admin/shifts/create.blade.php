@extends('layouts.admin')

@section('title', 'Create Shift')
@section('header', 'Create Shift')

@section('content')
    <div class="w-full">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                <i class="hgi-stroke text-[20px] hgi-calendar-01 text-indigo-600"></i>
                <div>
                    <h3 class="text-md font-semibold text-gray-800">Create Shift</h3>
                    <p class="text-xs text-gray-400">Open a new cash register shift</p>
                </div>
            </div>
            <form action="{{ route('admin.shifts.store') }}" method="POST">
                @csrf
                <div class="p-6">
                    @if(session('error'))
                        <div class="bg-red-50 text-red-600 p-4 rounded-lg mb-6 text-sm">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Outlet</label>
                            <select name="outlet_id" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                                <option value="">Select Outlet</option>
                                @foreach($outlets as $outlet)
                                    <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                                @endforeach
                            </select>
                            @error('outlet_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Assign User</label>
                            <select name="user_id" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                                <option value="">Select User</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ $user->id == auth()->id() ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Opening Cash (RM)</label>
                            <input type="number" name="opening_cash" step="0.01" min="0" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                value="0.00">
                            @error('opening_cash') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Notes (Optional)</label>
                            <textarea name="notes" rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"></textarea>
                            @error('notes') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3">
                        <a href="{{ route('admin.shifts.index') }}" class="btn btn-secondary">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            Open Shift
                        </button>
                    </div>
            </form>
        </div>
    </div>
@endsection