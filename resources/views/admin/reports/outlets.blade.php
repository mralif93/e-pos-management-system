@extends('layouts.admin')

@section('title', 'Outlet Reports')
@section('header', 'Cross-Outlet Performance')

@section('content')
    <!-- Filter Card -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm mb-6">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
            <i class="hgi-stroke text-[20px] hgi-settings-02 text-indigo-600"></i>
            <h3 class="text-md font-semibold text-gray-800">Search & Filter</h3>
        </div>
        <form method="GET">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                    <!-- Column 1: Date Range -->
                    <div class="flex items-center gap-4">
                        <input type="date" name="from" value="{{ request('from', now()->startOfMonth()->format('Y-m-d')) }}"
                            placeholder="Date From"
                            class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                        <span class="text-gray-400">-</span>
                        <input type="date" name="to" value="{{ request('to', now()->format('Y-m-d')) }}"
                            placeholder="Date To"
                            class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                    </div>

                    <!-- Column 2: Filters & Actions -->
                    <div class="flex flex-wrap items-center gap-4">
                        <div class="flex gap-2">
                            <a href="{{ route('admin.reports.outlets') }}"
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
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @foreach($outlets as $outlet)
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                    <i class="hgi-stroke text-[20px] hgi-store-01 text-indigo-600"></i>
                    <div>
                        <h3 class="text-md font-semibold text-gray-800">{{ $outlet->name }}</h3>
                        <p class="text-xs text-gray-400">{{ $outlet->address ?? 'No address' }}</p>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center gap-2 mb-1">
                                <i class="hgi-stroke text-[20px] hgi-cash-01 text-green-600 text-sm"></i>
                                <p class="text-xs text-gray-500">Total Sales</p>
                            </div>
                            <p class="text-lg font-bold text-gray-800">RM
                                {{ number_format($outletStats[$outlet->id]['total_sales'] ?? 0, 2) }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center gap-2 mb-1">
                                <i class="hgi-stroke text-[20px] hgi-invoice-01 text-blue-600 text-sm"></i>
                                <p class="text-xs text-gray-500">Transactions</p>
                            </div>
                            <p class="text-lg font-bold text-gray-800">
                                {{ number_format($outletStats[$outlet->id]['transactions'] ?? 0) }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center gap-2 mb-1">
                                <i class="hgi-stroke text-[20px] hgi-package text-orange-600 text-sm"></i>
                                <p class="text-xs text-gray-500">Products Sold</p>
                            </div>
                            <p class="text-lg font-bold text-gray-800">
                                {{ number_format($outletStats[$outlet->id]['items_sold'] ?? 0) }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center gap-2 mb-1">
                                <i class="hgi-stroke text-[20px] hgi-chart-increase text-purple-600 text-sm"></i>
                                <p class="text-xs text-gray-500">Avg. Transaction</p>
                            </div>
                            <p class="text-lg font-bold text-gray-800">RM
                                {{ number_format($outletStats[$outlet->id]['avg_transaction'] ?? 0, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if($outlets->isEmpty())
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-8 text-center text-gray-400">
            No outlets found
        </div>
    @endif
@endsection