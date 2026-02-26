@extends('layouts.admin')

@section('title', 'Inventory Reports')
@section('header', 'Inventory Reports')

@section('content')
    <!-- Filter Card -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm mb-6">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
            <i class="hgi-stroke text-[20px] hgi-settings-02 text-indigo-600"></i>
            <h3 class="font-semibold text-gray-800">Search & Filter</h3>
        </div>
        <form method="GET">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                    <!-- Column 1: Outlet -->
                    <div>
                        <select name="outlet_id"
                            class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                            <option value="">Outlet</option>
                            @foreach($outlets as $outlet)
                                <option value="{{ $outlet->id }}" {{ request('outlet_id') == $outlet->id ? 'selected' : '' }}>
                                    {{ $outlet->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Column 2: Filters & Actions -->
                    <div class="flex flex-wrap items-center gap-4">
                        <select name="status"
                            class="flex-1 px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                            <option value="">Stock Status</option>
                            <option value="low" {{ request('status') == 'low' ? 'selected' : '' }}>Low Stock</option>
                            <option value="out" {{ request('status') == 'out' ? 'selected' : '' }}>Out of Stock</option>
                        </select>

                        <div class="flex gap-2">
                            <a href="{{ route('admin.reports.inventory') }}"
                                class="px-3 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 flex items-center justify-center gap-1 text-sm bg-white">
                                <i class="hgi-stroke text-[20px] hgi-setup-01 text-sm"></i>
                                Reset
                            </a>
                            <button type="submit"
                                class="px-3 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center justify-center gap-1 text-sm">
                                <i class="hgi-stroke text-[20px] hgi-filter text-sm"></i>
                                Filter
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-500">Show</span>
                    <select name="per_page" class="px-2 py-1 border border-gray-300 rounded-lg text-sm"
                        onchange="this.form.submit()">
                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <i class="hgi-stroke text-[20px] hgi-package text-indigo-600"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500">Total Products</p>
                    <p class="text-xl font-bold text-gray-800">{{ number_format($totalProducts) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="hgi-stroke text-[20px] hgi-inbox text-green-600"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500">Total Stock</p>
                    <p class="text-xl font-bold text-gray-800">{{ number_format($totalStock) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="hgi-stroke text-[20px] hgi-alert-02 text-yellow-600"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500">Low Stock Items</p>
                    <p class="text-xl font-bold text-yellow-600">{{ number_format($lowStockCount) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="hgi-stroke text-[20px] hgi-alert-01 text-red-600"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500">Out of Stock</p>
                    <p class="text-xl font-bold text-red-600">{{ number_format($outOfStockCount) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="hgi-stroke text-[20px] hgi-warehouse text-indigo-600"></i>
                <div>
                    <h3 class="font-semibold text-gray-800">Inventory Records</h3>
                    <p class="text-xs text-gray-400">View stock levels by outlet</p>
                </div>
            </div>
        </div>
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Outlet</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Threshold</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($inventory as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ $item->product->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $item->product->sku ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $item->outlet->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ $item->quantity }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $item->product->low_stock_threshold ?? 10 }}</td>
                        <td class="px-6 py-4">
                            @if($item->quantity <= 0)
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700">Out of Stock</span>
                            @elseif($item->quantity <= ($item->product->low_stock_threshold ?? 10))
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-700">Low
                                    Stock</span>
                            @else
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">In Stock</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-400">No inventory data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 border-t border-gray-100">
            @if($inventory->total() <= $inventory->perPage())
                <p class="text-sm text-gray-500">
                    Showing {{ $inventory->firstItem() ?? 0 }} &ndash; {{ $inventory->lastItem() ?? 0 }} of
                    {{ $inventory->total() }} results
                </p>
            @else
                {{ $inventory->links() }}
            @endif
        </div>
    </div>
@endsection