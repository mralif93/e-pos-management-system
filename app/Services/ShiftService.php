<?php

namespace App\Services;

use App\Models\Shift;
use App\Models\Sale;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ShiftService
{
    public function openShift(int $outletId, int $userId, float $openingCash): Shift
    {
        $existingOpen = Shift::getOpenShift($outletId, $userId);
        
        if ($existingOpen) {
            throw new \Exception('You already have an open shift.');
        }

        return Shift::create([
            'outlet_id' => $outletId,
            'user_id' => $userId,
            'opening_cash' => $openingCash,
            'status' => 'open',
            'opened_at' => Carbon::now(),
        ]);
    }

    public function closeShift(Shift $shift, array $data): Shift
    {
        $salesSummary = $this->getShiftSalesSummary($shift);

        $data['total_sales'] = $salesSummary['total_sales'];
        $data['transaction_count'] = $salesSummary['transaction_count'];
        $data['card_total'] = $salesSummary['card_total'];
        $data['other_total'] = $salesSummary['other_total'];
        $data['expected_cash'] = $shift->opening_cash + $salesSummary['cash_total'];

        return $shift->close($data);
    }

    public function getShiftSalesSummary(Shift $shift): array
    {
        $sales = Sale::where('outlet_id', $shift->outlet_id)
            ->where('user_id', $shift->user_id)
            ->whereBetween('created_at', [$shift->opened_at, $shift->closed_at ?? Carbon::now()])
            ->where('status', '!=', 'void')
            ->with('payments')
            ->get();

        $totalSales = $sales->sum('total_amount');
        $transactionCount = $sales->count();
        
        $cashTotal = 0;
        $cardTotal = 0;
        $otherTotal = 0;

        foreach ($sales as $sale) {
            foreach ($sale->payments as $payment) {
                $method = strtolower($payment->payment_method);
                
                if (str_contains($method, 'cash')) {
                    $cashTotal += $payment->amount;
                } elseif (str_contains($method, 'card') || str_contains($method, 'credit')) {
                    $cardTotal += $payment->amount;
                } else {
                    $otherTotal += $payment->amount;
                }
            }
        }

        return [
            'total_sales' => $totalSales,
            'transaction_count' => $transactionCount,
            'cash_total' => $cashTotal,
            'card_total' => $cardTotal,
            'other_total' => $otherTotal,
        ];
    }

    public function getCurrentShift(int $outletId, int $userId): ?Shift
    {
        return Shift::getOpenShift($outletId, $userId);
    }

    public function getShiftHistory(int $outletId, ?Carbon $startDate = null, ?Carbon $endDate = null): Collection
    {
        $startDate = $startDate ?? Carbon::now()->startOfMonth();
        $endDate = $endDate ?? Carbon::now()->endOfMonth();

        return Shift::where('outlet_id', $outletId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with(['user', 'closedByUser'])
            ->orderBy('opened_at', 'desc')
            ->get();
    }

    public function getShiftReport(Shift $shift): array
    {
        $salesSummary = $this->getShiftSalesSummary($shift);
        
        return [
            'shift' => $shift,
            'sales_summary' => $salesSummary,
            'cash_difference' => $shift->closing_cash - $shift->expected_cash,
            'variance_percent' => $shift->expected_cash > 0 
                ? (($shift->closing_cash - $shift->expected_cash) / $shift->expected_cash) * 100 
                : 0,
        ];
    }

    public function getAllOpenShifts(): Collection
    {
        return Shift::where('status', 'open')
            ->with(['outlet', 'user'])
            ->get();
    }
}
