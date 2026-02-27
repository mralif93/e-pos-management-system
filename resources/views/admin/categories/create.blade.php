@extends('layouts.admin')

@section('title', 'Create Category')
@section('header', 'Create Category')

@section('content')
    <div class="w-full">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                <i class="hgi-stroke text-[20px] hgi-grid-view text-indigo-600"></i>
                <div>
                    <h3 class="text-md font-semibold text-gray-800">Create Category</h3>
                    <p class="text-xs text-gray-400">Add a new category for products</p>
                </div>
            </div>
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category Name</label>
                            <input type="text" name="name" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3">
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Create Category
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection