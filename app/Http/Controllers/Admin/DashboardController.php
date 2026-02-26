<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Outlet;
use App\Models\Shift;
use App\Models\InventoryTransfer;
use App\Models\LowStockAlert;
use App\Services\CrossOutletReportService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $thisYear = Carbon::now()->startOfYear();
        $outletId = request('outlet_id');

        // Today's stats
        $todaySales = Sale::whereDate('created_at', $today)
            ->where('status', '!=', 'void')
            ->when($outletId, fn($q) => $q->where('outlet_id', $outletId))
            ->sum('total_amount');

        $todayTransactions = Sale::whereDate('created_at', $today)
            ->where('status', '!=', 'void')
            ->when($outletId, fn($q) => $q->where('outlet_id', $outletId))
            ->count();

        // This month
        $monthSales = Sale::whereBetween('created_at', [$thisMonth, Carbon::now()])
            ->where('status', '!=', 'void')
            ->when($outletId, fn($q) => $q->where('outlet_id', $outletId))
            ->sum('total_amount');

        $monthTransactions = Sale::whereBetween('created_at', [$thisMonth, Carbon::now()])
            ->where('status', '!=', 'void')
            ->when($outletId, fn($q) => $q->where('outlet_id', $outletId))
            ->count();

        // This year
        $yearSales = Sale::whereBetween('created_at', [$thisYear, Carbon::now()])
            ->where('status', '!=', 'void')
            ->when($outletId, fn($q) => $q->where('outlet_id', $outletId))
            ->sum('total_amount');

        // Top products today
        $topProductsQuery = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->whereDate('sales.created_at', $today)
            ->where('sales.status', '!=', 'void');

        if ($outletId) {
            $topProductsQuery->where('sales.outlet_id', $outletId);
        }

        $topProducts = $topProductsQuery->selectRaw('products.id, products.name, SUM(sale_items.quantity) as total_qty, SUM(sale_items.quantity * sale_items.price) as total_revenue')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->get();

        // Recent transactions
        $recentSales = Sale::with(['customer', 'user', 'outlet'])
            ->where('status', '!=', 'void')
            ->when($outletId, fn($q) => $q->where('outlet_id', $outletId))
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Low stock alerts
        $lowStockCount = LowStockAlert::where('status', 'pending')
            ->when($outletId, fn($q) => $q->where('outlet_id', $outletId))
            ->count();

        // Pending transfers
        $pendingTransfers = InventoryTransfer::whereIn('status', ['pending', 'approved', 'in_transit'])
            ->when($outletId, fn($q) => $q->where('from_outlet_id', $outletId)->orWhere('to_outlet_id', $outletId))
            ->count();

        // Open shifts
        $openShifts = Shift::where('status', 'open')
            ->when($outletId, fn($q) => $q->where('outlet_id', $outletId))
            ->count();

        // Outlets count
        $outletsCount = Outlet::count();
        $outlets = Outlet::orderBy('name')->get();

        // Products count
        $productsCount = Product::where('is_active', true)->count();

        // Customers count
        $customersCount = Customer::count();

        return view('admin.dashboard.index', compact(
            'todaySales',
            'todayTransactions',
            'monthSales',
            'monthTransactions',
            'yearSales',
            'topProducts',
            'recentSales',
            'lowStockCount',
            'pendingTransfers',
            'openShifts',
            'outletsCount',
            'productsCount',
            'customersCount',
            'outlets'
        ));
    }
}
