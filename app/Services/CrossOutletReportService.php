<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\Outlet;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CrossOutletReportService
{
    public function getOutletPerformance(?Carbon $startDate = null, ?Carbon $endDate = null): Collection
    {
        $startDate = $startDate ?? Carbon::now()->startOfMonth();
        $endDate = $endDate ?? Carbon::now()->endOfMonth();

        return Outlet::withCount(['users'])
            ->leftJoin('sales', function ($join) use ($startDate, $endDate) {
                $join->on('outlets.id', '=', 'sales.outlet_id')
                    ->whereBetween('sales.created_at', [$startDate, $endDate]);
            })
            ->selectRaw('
                outlets.id,
                outlets.name,
                outlets.outlet_code,
                COUNT(DISTINCT sales.id) as total_transactions,
                COALESCE(SUM(sales.total_amount), 0) as total_sales,
                COALESCE(SUM(sales.tax_amount), 0) as total_tax,
                COALESCE(SUM(sales.discount_amount), 0) as total_discount,
                COALESCE(AVG(sales.total_amount), 0) as average_transaction
            ')
            ->groupBy('outlets.id', 'outlets.name', 'outlets.outlet_code')
            ->orderByDesc('total_sales')
            ->get();
    }

    public function getTopProductsByOutlet(int $outletId, ?Carbon $startDate = null, ?Carbon $endDate = null, int $limit = 10): Collection
    {
        $startDate = $startDate ?? Carbon::now()->startOfMonth();
        $endDate = $endDate ?? Carbon::now()->endOfMonth();

        return DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->where('sales.outlet_id', $outletId)
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->where('sales.status', '!=', 'void')
            ->selectRaw('
                products.id,
                products.name,
                products.sku,
                SUM(sale_items.quantity) as total_quantity,
                SUM(sale_items.quantity * sale_items.price) as total_revenue,
                COUNT(DISTINCT sales.id) as transaction_count
            ')
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderByDesc('total_quantity')
            ->limit($limit)
            ->get();
    }

    public function getPaymentMethodBreakdown(?Carbon $startDate = null, ?Carbon $endDate = null): Collection
    {
        $startDate = $startDate ?? Carbon::now()->startOfMonth();
        $endDate = $endDate ?? Carbon::now()->endOfMonth();

        return DB::table('payments')
            ->join('sales', 'payments.sale_id', '=', 'sales.id')
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->where('sales.status', '!=', 'void')
            ->selectRaw('
                payments.payment_method,
                COUNT(payments.id) as transaction_count,
                SUM(payments.amount) as total_amount
            ')
            ->groupBy('payments.payment_method')
            ->orderByDesc('total_amount')
            ->get();
    }

    public function getHourlySalesTrend(?Carbon $date = null): Collection
    {
        $date = $date ?? Carbon::now();

        return Sale::whereDate('created_at', $date)
            ->where('status', '!=', 'void')
            ->selectRaw('
                HOUR(created_at) as hour,
                COUNT(*) as transaction_count,
                SUM(total_amount) as total_sales
            ')
            ->groupBy(DB::raw('HOUR(created_at)'))
            ->orderBy('hour')
            ->get();
    }

    public function getComparisonReport(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $startDate = $startDate ?? Carbon::now()->startOfMonth();
        $endDate = $endDate ?? Carbon::now()->endOfMonth();

        $previousStart = $startDate->copy()->subMonth();
        $previousEnd = $endDate->copy()->subMonth();

        $current = $this->getOutletPerformance($startDate, $endDate);
        $previous = $this->getOutletPerformance($previousStart, $previousEnd);

        $comparison = [];

        foreach ($current as $outlet) {
            $prev = $previous->firstWhere('id', $outlet->id);
            
            $salesChange = $prev ? (($outlet->total_sales - $prev->total_sales) / max($prev->total_sales, 1)) * 100 : 0;
            $transactionChange = $prev ? (($outlet->total_transactions - $prev->total_transactions) / max($prev->total_transactions, 1)) * 100 : 0;

            $comparison[] = [
                'outlet' => $outlet,
                'previous' => $prev,
                'sales_change_percent' => round($salesChange, 2),
                'transaction_change_percent' => round($transactionChange, 2),
            ];
        }

        return [
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
            ],
            'outlets' => collect($comparison)->sortByDesc('outlet.total_sales')->values()->all(),
        ];
    }

    public function getDailySalesSummary(?Carbon $date = null): Collection
    {
        $date = $date ?? Carbon::now();

        return Sale::whereDate('created_at', $date)
            ->where('status', '!=', 'void')
            ->join('outlets', 'sales.outlet_id', '=', 'outlets.id')
            ->selectRaw('
                outlets.id,
                outlets.name,
                outlets.outlet_code,
                COUNT(sales.id) as transactions,
                SUM(sales.total_amount) as total_sales,
                SUM(sales.tax_amount) as total_tax,
                SUM(sales.discount_amount) as total_discount
            ')
            ->groupBy('outlets.id', 'outlets.name', 'outlets.outlet_code')
            ->orderByDesc('total_sales')
            ->get();
    }

    public function exportToExcel(?Carbon $startDate = null, ?Carbon $endDate = null): string
    {
        $startDate = $startDate ?? Carbon::now()->startOfMonth();
        $endDate = $endDate ?? Carbon::now()->endOfMonth();

        $performance = $this->getOutletPerformance($startDate, $endDate);
        $payments = $this->getPaymentMethodBreakdown($startDate, $endDate);

        return json_encode([
            'generated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
            ],
            'outlet_performance' => $performance,
            'payment_breakdown' => $payments,
        ]);
    }
}
