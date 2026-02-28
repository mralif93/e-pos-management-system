<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Outlet;
use App\Models\ProductOutletPrice;
use App\Services\CrossOutletReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function sales(Request $request)
    {
        $query = Sale::with(['customer', 'user', 'outlet', 'saleItems']);

        if ($request->outlet_id) {
            $query->where('outlet_id', $request->outlet_id);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', Carbon::parse($request->date_from));
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', Carbon::parse($request->date_to));
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $perPage = $request->per_page ?? 10;
        $sales = $query->orderBy('created_at', 'desc')->paginate($perPage);

        $totalSales = $query->sum('total_amount');
        $totalTax = $query->sum('tax_amount');
        $totalDiscount = $query->sum('discount_amount');
        $transactions = $query->count();

        $salesIds = $sales->pluck('id');
        $itemsSold = $salesIds->isNotEmpty()
            ? \DB::table('sale_items')->whereIn('sale_id', $salesIds)->sum('quantity')
            : 0;

        $outlets = Outlet::where('is_active', true)->get();

        return view('admin.reports.sales', compact('sales', 'outlets', 'totalSales', 'totalTax', 'totalDiscount', 'transactions', 'itemsSold'));
    }

    public function inventory(Request $request)
    {
        $query = ProductOutletPrice::with(['product', 'outlet']);

        if ($request->outlet_id) {
            $query->where('outlet_id', $request->outlet_id);
        }

        if ($request->status === 'low') {
            $query->whereRaw('stock_level <= (SELECT low_stock_threshold FROM products WHERE products.id = product_outlet_prices.product_id)');
        } elseif ($request->status === 'out') {
            $query->where('stock_level', '<=', 0);
        }

        $perPage = $request->per_page ?? 10;
        $inventory = $query->orderBy('stock_level', 'asc')->paginate($perPage);

        $totalProducts = \App\Models\Product::count();
        $totalStock = ProductOutletPrice::sum('stock_level');
        $lowStockCount = ProductOutletPrice::whereRaw('stock_level <= (SELECT low_stock_threshold FROM products WHERE products.id = product_outlet_prices.product_id AND products.low_stock_threshold > 0)')->count();
        $outOfStockCount = ProductOutletPrice::where('stock_level', '<=', 0)->count();

        $outlets = Outlet::where('is_active', true)->get();

        return view('admin.reports.inventory', compact('inventory', 'outlets', 'totalProducts', 'totalStock', 'lowStockCount', 'outOfStockCount'));
    }

    public function outlets(Request $request)
    {
        $startDate = $request->from ? Carbon::parse($request->from) : Carbon::now()->startOfMonth();
        $endDate = $request->to ? Carbon::parse($request->to) : Carbon::now();

        $outlets = Outlet::where('is_active', true)->get();

        $outletStats = [];
        foreach ($outlets as $outlet) {
            $sales = Sale::where('outlet_id', $outlet->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('status', '!=', 'void');

            $outletStats[$outlet->id] = [
                'total_sales' => $sales->sum('total_amount'),
                'transactions' => $sales->count(),
                'items_sold' => \DB::table('sale_items')
                    ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                    ->where('sales.outlet_id', $outlet->id)
                    ->whereBetween('sales.created_at', [$startDate, $endDate])
                    ->where('sales.status', '!=', 'void')
                    ->sum('sale_items.quantity'),
                'avg_transaction' => $sales->count() > 0 ? $sales->sum('total_amount') / $sales->count() : 0,
            ];
        }

        return view('admin.reports.outlets', compact('outlets', 'outletStats', 'startDate', 'endDate'));
    }

    public function outletPerformance(Request $request)
    {
        $startDate = $request->date_from ? Carbon::parse($request->date_from) : Carbon::now()->startOfMonth();
        $endDate = $request->date_to ? Carbon::parse($request->date_to) : Carbon::now()->endOfMonth();

        $reportService = new CrossOutletReportService();
        $performance = $reportService->getOutletPerformance($startDate, $endDate);
        $comparison = $reportService->getComparisonReport($startDate, $endDate);

        return view('admin.reports.outlet-performance', compact('performance', 'comparison', 'startDate', 'endDate'));
    }

    public function products(Request $request)
    {
        $outletId = $request->outlet_id;
        $startDate = $request->date_from ? Carbon::parse($request->date_from) : Carbon::now()->startOfMonth();
        $endDate = $request->date_to ? Carbon::parse($request->date_to) : Carbon::now()->endOfMonth();

        $reportService = new CrossOutletReportService();
        $outlets = Outlet::where('is_active', true)->get();

        $products = $outletId
            ? $reportService->getTopProductsByOutlet($outletId, $startDate, $endDate, 50)
            : collect([]);

        return view('admin.reports.products', compact('products', 'outlets', 'outletId', 'startDate', 'endDate'));
    }
}
