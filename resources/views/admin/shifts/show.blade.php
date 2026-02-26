@extends('layouts.admin')

@section('title', 'Shift Details')
@section('header', 'Shift ' . $shift->shift_number)

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('admin.shifts.index') }}"
            class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-1 shadow-sm">
            <i class="hgi-stroke text-[20px] hgi-arrow-left-01 mr-1 text-sm"></i>
            Back to Shifts
        </a>
        <div class="flex gap-2">
            <a href="{{ route('admin.shifts.edit', $shift->id) }}"
                class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-1">
                <i class="hgi-stroke text-[20px] hgi-edit-02 text-sm"></i>
                Edit
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Details -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                <h3 class="font-semibold text-gray-800 mb-4 border-b pb-2">Shift Overview</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Outlet</p>
                        <p class="font-medium text-gray-900">{{ $shift->outlet->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Assigned Staff</p>
                        <p class="font-medium text-gray-900">{{ $shift->user->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Status</p>
                        <span
                            class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $shift->status === 'open' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($shift->status) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Opened At</p>
                        <p class="font-medium text-gray-900">
                            {{ $shift->opened_at ? $shift->opened_at->format('d M Y, h:i A') : 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Closed At</p>
                        <p class="font-medium text-gray-900">
                            {{ $shift->closed_at ? $shift->closed_at->format('d M Y, h:i A') : 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Closed By</p>
                        <p class="font-medium text-gray-900">{{ $shift->closedByUser->name ?? 'N/A' }}</p>
                    </div>
                </div>

                @if($shift->notes)
                    <div class="mt-6">
                        <p class="text-xs text-gray-500 mb-1">Notes</p>
                        <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded-lg">{{ $shift->notes }}</p>
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                <h3 class="font-semibold text-gray-800 mb-4 border-b pb-2">Sales Summary</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <i class="hgi-stroke text-[20px] hgi-invoice-01 text-indigo-500 text-lg"></i>
                            <p class="text-xs text-gray-500">Transactions</p>
                        </div>
                        <p class="text-xl font-bold text-gray-900">{{ number_format($salesSummary['transaction_count']) }}
                        </p>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <i class="hgi-stroke text-[20px] hgi-cash-01 text-green-500 text-lg"></i>
                            <p class="text-xs text-gray-500">Total Sales</p>
                        </div>
                        <p class="text-xl font-bold text-gray-900">RM {{ number_format($salesSummary['total_sales'], 2) }}
                        </p>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <i class="hgi-stroke text-[20px] hgi-credit-card text-blue-500 text-lg"></i>
                            <p class="text-xs text-gray-500">Card Payment</p>
                        </div>
                        <p class="text-xl font-bold text-gray-900">RM {{ number_format($salesSummary['card_total'], 2) }}
                        </p>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <i class="hgi-stroke text-[20px] hgi-money-01 text-orange-500 text-lg"></i>
                            <p class="text-xs text-gray-500">Cash Payment</p>
                        </div>
                        <p class="text-xl font-bold text-gray-900">RM {{ number_format($salesSummary['cash_total'], 2) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Cash Handling -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                <h3 class="font-semibold text-gray-800 mb-4 border-b pb-2">Cash Handling</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center pb-2 border-b border-gray-50">
                        <span class="text-sm text-gray-500">Opening Cash</span>
                        <span class="font-medium text-gray-900">RM {{ number_format($shift->opening_cash, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center pb-2 border-b border-gray-50">
                        <span class="text-sm text-gray-500">Cash Sales</span>
                        <span class="font-medium text-green-600">+ RM
                            {{ number_format($salesSummary['cash_total'], 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center pb-2 border-b border-gray-50 bg-gray-50 p-2 rounded">
                        <span class="text-sm font-semibold text-gray-700">Expected Cash in Drawer</span>
                        <span class="font-bold text-gray-900">RM
                            {{ number_format($shift->expected_cash ?? ($shift->opening_cash + $salesSummary['cash_total']), 2) }}</span>
                    </div>

                    @if($shift->status === 'closed')
                        <div class="flex justify-between items-center pb-2 border-b border-gray-50">
                            <span class="text-sm text-gray-500">Actual Closing Cash</span>
                            <span class="font-medium text-indigo-600">RM {{ number_format($shift->closing_cash, 2) }}</span>
                        </div>
                        <div
                            class="flex justify-between items-center p-3 rounded-lg {{ $shift->cash_difference == 0 ? 'bg-green-50' : 'bg-red-50' }}">
                            <span
                                class="text-sm font-semibold {{ $shift->cash_difference == 0 ? 'text-green-800' : 'text-red-800' }}">Difference
                                / Variance</span>
                            <span class="font-bold {{ $shift->cash_difference == 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $shift->cash_difference > 0 ? '+' : '' }}RM {{ number_format($shift->cash_difference, 2) }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection