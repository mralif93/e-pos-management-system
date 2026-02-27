@extends('layouts.admin')

@section('title', 'Create Product')
@section('header', 'Create Product')

@section('content')
    <div class="w-full">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                <i class="hgi-stroke text-[20px] hgi-package text-indigo-600"></i>
                <div>
                    <h3 class="text-md font-semibold text-gray-800">Create Product</h3>
                    <p class="text-xs text-gray-400">Add a new product to your inventory</p>
                </div>
            </div>
            <form action="{{ route('admin.products.store') }}" method="POST">
                @csrf
                <div class="p-6">
                    @if ($errors->any())
                        <div class="bg-red-50 text-red-600 p-4 rounded-lg mb-6 text-sm">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Product Name</label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
                            <input type="text" name="sku" value="{{ old('sku') }}" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                            <select name="category_id" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Price (RM)</label>
                            <input type="number" name="price" step="0.01" value="{{ old('price') }}" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Stock Level</label>
                            <input type="number" name="stock_level" value="{{ old('stock_level') }}" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Low Stock Threshold</label>
                            <input type="number" name="low_stock_threshold" value="{{ old('low_stock_threshold', 10) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3">
                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Create Product
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection