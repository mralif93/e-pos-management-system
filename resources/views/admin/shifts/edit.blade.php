@extends('layouts.admin')

@section('title', 'Edit Shift')
@section('header', 'Edit Shift: ' . $shift->shift_number)

@section('content')
    <div class="w-full">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                <i class="hgi-stroke text-[20px] hgi-calendar-01 text-indigo-600"></i>
                <div>
                    <h3 class="text-md font-semibold text-gray-800">Edit Shift</h3>
                    <p class="text-xs text-gray-400">Update shift details</p>
                </div>
            </div>
            <form action="{{ route('admin.shifts.update', $shift->id) }}" method="POST">
                @csrf
                @method('PUT')
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
                                    <option value="{{ $outlet->id }}" {{ $shift->outlet_id == $outlet->id ? 'selected' : '' }}>
                                        {{ $outlet->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('outlet_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Assigned User</label>
                            <select name="user_id" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                                <option value="">Select User</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ $shift->user_id == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Opening Cash (RM)</label>
                            <input type="number" name="opening_cash" step="0.01" min="0" required
                                value="{{ $shift->opening_cash }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                            @error('opening_cash') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                                <option value="open" {{ $shift->status === 'open' ? 'selected' : '' }}>Open</option>
                                <option value="closed" {{ $shift->status === 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                            @error('status') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Notes (Optional)</label>
                            <textarea name="notes" rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">{{ $shift->notes }}</textarea>
                            @error('notes') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3">
                        <a href="{{ route('admin.shifts.index') }}" class="btn btn-secondary">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            Save Changes
                        </button>
                    </div>
            </form>
        </div>
    </div>
@endsection