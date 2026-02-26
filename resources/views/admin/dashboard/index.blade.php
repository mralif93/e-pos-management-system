@extends('layouts.admin')

@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')

    {{-- ── Welcome Banner ── --}}
    <div
        class="bg-gradient-to-r from-indigo-600 to-indigo-500 rounded-xl p-6 mb-6 flex items-center justify-between overflow-hidden relative">
        <div class="relative z-10">
            <p class="text-indigo-100 text-sm font-medium mb-1">{{ now()->format('l, d F Y') }}</p>
            <h2 class="text-white text-2xl font-bold">Welcome back, {{ auth()->user()->name }} 👋</h2>
            <p class="text-indigo-200 text-sm mt-1">Here's what's happening with your store today.</p>
        </div>
        <div class="flex gap-3 relative z-10">
            <a href="{{ route('admin.shifts.create') }}"
                class="bg-white/20 hover:bg-white/30 text-white text-sm font-medium px-4 py-2 rounded-lg flex items-center gap-2 transition-all border border-white/20">
                <i class="hgi-stroke text-[18px] hgi-add-01"></i> New Shift
            </a>
            <a href="{{ route('admin.reports.sales') }}"
                class="bg-white text-indigo-600 text-sm font-medium px-4 py-2 rounded-lg flex items-center gap-2 hover:bg-indigo-50 transition-all shadow-sm">
                <i class="hgi-stroke text-[18px] hgi-chart-increase"></i> Sales Report
            </a>
        </div>
        {{-- Decorative circles --}}
        <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/5 rounded-full"></div>
        <div class="absolute -right-4 -bottom-12 w-56 h-56 bg-white/5 rounded-full"></div>
    </div>

    {{-- ── Primary KPI Cards ── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">

        {{-- Today's Sales --}}
        <div
            class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex items-start justify-between group hover:border-green-200 hover:shadow-md transition-all">
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Today's Sales</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">RM {{ number_format($todaySales, 2) }}</p>
                <p class="text-xs text-gray-400 mt-1.5 flex items-center gap-1">
                    <i class="hgi-stroke text-[14px] hgi-invoice-01"></i>
                    {{ $todayTransactions }} transactions
                </p>
            </div>
            <div
                class="w-11 h-11 bg-green-100 rounded-xl flex items-center justify-center group-hover:bg-green-200 transition-colors shrink-0">
                <i class="hgi-stroke text-[22px] hgi-wallet-01 text-green-600"></i>
            </div>
        </div>

        {{-- This Month --}}
        <div
            class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex items-start justify-between group hover:border-indigo-200 hover:shadow-md transition-all">
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">This Month</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">RM {{ number_format($monthSales, 2) }}</p>
                <p class="text-xs text-gray-400 mt-1.5 flex items-center gap-1">
                    <i class="hgi-stroke text-[14px] hgi-invoice-01"></i>
                    {{ $monthTransactions }} transactions
                </p>
            </div>
            <div
                class="w-11 h-11 bg-indigo-100 rounded-xl flex items-center justify-center group-hover:bg-indigo-200 transition-colors shrink-0">
                <i class="hgi-stroke text-[22px] hgi-calendar-01 text-indigo-600"></i>
            </div>
        </div>

        {{-- This Year --}}
        <div
            class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex items-start justify-between group hover:border-blue-200 hover:shadow-md transition-all">
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">This Year</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">RM {{ number_format($yearSales, 2) }}</p>
                <p class="text-xs text-gray-400 mt-1.5 flex items-center gap-1">
                    <i class="hgi-stroke text-[14px] hgi-chart-increase"></i>
                    Cumulative revenue
                </p>
            </div>
            <div
                class="w-11 h-11 bg-blue-100 rounded-xl flex items-center justify-center group-hover:bg-blue-200 transition-colors shrink-0">
                <i class="hgi-stroke text-[22px] hgi-chart-increase text-blue-600"></i>
            </div>
        </div>

        {{-- Alerts --}}
        <div
            class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex items-start justify-between group hover:border-red-200 hover:shadow-md transition-all">
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Pending Alerts</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ $lowStockCount + $pendingTransfers }}</p>
                <p class="text-xs text-gray-400 mt-1.5">
                    <span class="{{ $lowStockCount > 0 ? 'text-red-500 font-medium' : '' }}">{{ $lowStockCount }} low
                        stock</span>
                    &bull;
                    <span class="{{ $pendingTransfers > 0 ? 'text-amber-500 font-medium' : '' }}">{{ $pendingTransfers }}
                        transfers</span>
                </p>
            </div>
            <div
                class="w-11 h-11 bg-red-100 rounded-xl flex items-center justify-center group-hover:bg-red-200 transition-colors shrink-0">
                <i class="hgi-stroke text-[22px] hgi-notification-01 text-red-600"></i>
            </div>
        </div>

    </div>

    {{-- ── Secondary Stats + Quick Actions ── --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">

        <a href="{{ route('admin.outlets.index') }}"
            class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-3 hover:border-amber-200 hover:shadow-md transition-all group">
            <div
                class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center group-hover:bg-amber-200 transition-colors shrink-0">
                <i class="hgi-stroke text-[20px] hgi-store-01 text-amber-600"></i>
            </div>
            <div>
                <p class="text-xs text-gray-400">Outlets</p>
                <p class="text-xl font-bold text-gray-800">{{ $outletsCount }}</p>
            </div>
        </a>

        <a href="{{ route('admin.products.index') }}"
            class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-3 hover:border-purple-200 hover:shadow-md transition-all group">
            <div
                class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center group-hover:bg-purple-200 transition-colors shrink-0">
                <i class="hgi-stroke text-[20px] hgi-package text-purple-600"></i>
            </div>
            <div>
                <p class="text-xs text-gray-400">Products</p>
                <p class="text-xl font-bold text-gray-800">{{ $productsCount }}</p>
            </div>
        </a>

        <a href="{{ route('admin.customers.index') }}"
            class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-3 hover:border-pink-200 hover:shadow-md transition-all group">
            <div
                class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center group-hover:bg-pink-200 transition-colors shrink-0">
                <i class="hgi-stroke text-[20px] hgi-user-multiple-02 text-pink-600"></i>
            </div>
            <div>
                <p class="text-xs text-gray-400">Customers</p>
                <p class="text-xl font-bold text-gray-800">{{ $customersCount }}</p>
            </div>
        </a>

        <a href="{{ route('admin.shifts.index') }}"
            class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-3 hover:border-teal-200 hover:shadow-md transition-all group">
            <div
                class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center group-hover:bg-teal-200 transition-colors shrink-0">
                <i class="hgi-stroke text-[20px] hgi-time-02 text-teal-600"></i>
            </div>
            <div>
                <p class="text-xs text-gray-400">Open Shifts</p>
                <p class="text-xl font-bold {{ $openShifts > 0 ? 'text-teal-600' : 'text-gray-800' }}">{{ $openShifts }}</p>
            </div>
        </a>

    </div>

    {{-- ── Main Content Row ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

        {{-- Top Products (3/5) --}}
        <div class="lg:col-span-3 bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="hgi-stroke text-[20px] hgi-star text-indigo-600"></i>
                    <div>
                        <h3 class="font-semibold text-gray-800">Top Products</h3>
                        <p class="text-xs text-gray-400">Best sellers today</p>
                    </div>
                </div>
                <a href="{{ route('admin.products.index') }}"
                    class="text-xs font-medium text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                    View All <i class="hgi-stroke text-[14px] hgi-arrow-right-01"></i>
                </a>
            </div>
            <div class="p-6">
                @if($topProducts->count() > 0)
                    <div class="space-y-3">
                        @foreach($topProducts as $index => $product)
                            <div class="flex items-center gap-4">
                                <span
                                    class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold shrink-0
                                                {{ $index === 0 ? 'bg-yellow-100 text-yellow-700' : ($index === 1 ? 'bg-gray-100 text-gray-600' : ($index === 2 ? 'bg-amber-100 text-amber-700' : 'bg-gray-50 text-gray-400')) }}">
                                    {{ $index + 1 }}
                                </span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800 truncate">{{ $product->name }}</p>
                                    <div class="w-full bg-gray-100 rounded-full h-1 mt-1">
                                        @php
                                            $maxRevenue = $topProducts->max('total_revenue');
                                            $pct = $maxRevenue > 0 ? round(($product->total_revenue / $maxRevenue) * 100) : 0;
                                        @endphp
                                        <div class="bg-indigo-500 h-1 rounded-full" style="width: {{ $pct }}%"></div>
                                    </div>
                                </div>
                                <div class="text-right shrink-0">
                                    <p class="text-sm font-bold text-gray-800">RM {{ number_format($product->total_revenue, 2) }}
                                    </p>
                                    <p class="text-xs text-gray-400">{{ $product->total_qty }} sold</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-10">
                        <i class="hgi-stroke text-[40px] hgi-package text-gray-200 block mb-3"></i>
                        <p class="text-sm text-gray-400">No sales recorded today</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Recent Transactions (2/5) --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="hgi-stroke text-[20px] hgi-invoice-01 text-indigo-600"></i>
                    <div>
                        <h3 class="font-semibold text-gray-800">Recent Transactions</h3>
                        <p class="text-xs text-gray-400">Latest 5 sales</p>
                    </div>
                </div>
                <a href="{{ route('admin.reports.sales') }}"
                    class="text-xs font-medium text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                    View All <i class="hgi-stroke text-[14px] hgi-arrow-right-01"></i>
                </a>
            </div>
            <div class="divide-y divide-gray-50">
                @if($recentSales->count() > 0)
                    @foreach($recentSales as $sale)
                        <div class="px-5 py-3.5 flex items-center justify-between hover:bg-gray-50 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center shrink-0">
                                    <i class="hgi-stroke text-[16px] hgi-invoice-01 text-indigo-500"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">#{{ $sale->id }}</p>
                                    <p class="text-xs text-gray-400">{{ $sale->created_at->format('H:i') }} &bull;
                                        {{ $sale->user->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-gray-800">RM {{ number_format($sale->total_amount, 2) }}</p>
                                <span
                                    class="text-xs px-2 py-0.5 rounded-full {{ $sale->status === 'completed' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                                    {{ ucfirst($sale->status) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-10">
                        <i class="hgi-stroke text-[40px] hgi-invoice-01 text-gray-200 block mb-3"></i>
                        <p class="text-sm text-gray-400">No transactions yet today</p>
                    </div>
                @endif
            </div>
        </div>

    </div>

@endsection