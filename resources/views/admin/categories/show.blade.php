@extends('layouts.admin')

@section('title', $category->name)
@section('header', 'Category Details')

@section('content')
    {{-- Back & Actions --}}
    <div class="flex items-center justify-between mb-6">
        <a href="{{ route('admin.categories.index') }}"
            class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-1 shadow-sm">
            <i class="hgi-stroke hgi-arrow-left-01 text-[18px]"></i> Back to Categories
        </a>
        <a href="{{ route('admin.categories.edit', $category->id) }}"
            class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-1 shadow-sm">
            <i class="hgi-stroke hgi-edit-02 text-[18px]"></i> Edit
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Right: Info Card --}}
        <div class="space-y-6">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                        <i class="hgi-stroke hgi-grid-view text-[22px] text-indigo-600"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">{{ $category->name }}</h2>
                        <span
                            class="px-2 py-0.5 text-xs font-medium rounded-full {{ $category->is_active ?? true ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ ($category->is_active ?? true) ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
                <div class="space-y-3">
                    <div class="flex justify-between py-2 border-b border-gray-50">
                        <span class="text-sm text-gray-500">Total Products</span>
                        <span class="font-bold text-indigo-700 text-lg">{{ $category->products->count() }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-50">
                        <span class="text-sm text-gray-500">Created</span>
                        <span class="text-sm text-gray-700">{{ $category->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-sm text-gray-500">Last Updated</span>
                        <span class="text-sm text-gray-700">{{ $category->updated_at->format('d M Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Left: Products Table --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                    <i class="hgi-stroke hgi-package text-[20px] text-indigo-600"></i>
                    <h3 class="font-semibold text-gray-800">Products in this Category</h3>
                </div>
                @if($category->products->isEmpty())
                    <p class="px-6 py-10 text-center text-sm text-gray-400">No products in this category</p>
                @else
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($category->products as $product)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-3 text-sm font-medium text-gray-800">{{ $product->name }}</td>
                                    <td class="px-6 py-3 text-sm font-mono text-gray-600">{{ $product->sku ?? '—' }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-700">RM {{ number_format($product->price, 2) }}</td>
                                    <td
                                        class="px-6 py-3 text-sm {{ $product->stock_level <= 5 ? 'text-red-600 font-semibold' : 'text-gray-700' }}">
                                        {{ $product->stock_level }}
                                    </td>
                                    <td class="px-6 py-3">
                                        <span
                                            class="px-2 py-0.5 text-xs font-medium rounded-full {{ $product->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                            {{ $product->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 text-right">
                                        <a href="{{ route('admin.products.show', $product->id) }}"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-200 text-gray-500 bg-white hover:bg-indigo-50 hover:border-indigo-300 hover:text-indigo-600 transition-all">
                                            <i class="hgi-stroke hgi-view text-[16px]"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
@endsection