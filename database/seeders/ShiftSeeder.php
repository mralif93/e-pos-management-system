<?php

namespace Database\Seeders;

use App\Models\Shift;
use App\Models\User;
use App\Models\Outlet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $outlets = Outlet::all();

        foreach ($outlets as $outlet) {
            // Find cashiers for this outlet
            $cashiers = User::where('outlet_id', $outlet->id)
                ->where('role', 'Cashier')
                ->get();

            if ($cashiers->isEmpty()) {
                continue;
            }

            foreach ($cashiers as $cashier) {
                // Determine a unique sequence for shift reference
                $refNo = 'SHF-' . strtoupper(Str::random(6));

                // Create a closed shift from yesterday
                $yesterdayStart = Carbon::yesterday()->setHour(8)->setMinute(0);
                $yesterdayEnd = Carbon::yesterday()->setHour(17)->setMinute(0);

                Shift::create([
                    'outlet_id' => $outlet->id,
                    'user_id' => $cashier->id,
                    'shift_number' => $refNo . '-YEST',
                    'opening_cash' => 500.00,
                    'opened_at' => $yesterdayStart,
                    'closed_at' => $yesterdayEnd,
                    'closed_by' => $cashier->id,
                    'status' => 'closed',
                    'notes' => 'Regular shift, closed normally.',
                    'total_sales' => 1250.50,
                    'card_total' => 600.00,
                    'other_total' => 50.50,
                    'expected_cash' => 1100.00, // 500 opening + (1250.50 total - 600 card - 50.50 other)
                    'closing_cash' => 1100.00,
                    'cash_difference' => 0.00,
                    'transaction_count' => 12,
                ]);

                // Create an open shift for today
                $todayStart = Carbon::today()->setHour(8)->setMinute(0);

                Shift::create([
                    'outlet_id' => $outlet->id,
                    'user_id' => $cashier->id,
                    'shift_number' => $refNo . '-TODAY',
                    'opening_cash' => 500.00,
                    'opened_at' => $todayStart,
                    'closed_at' => null,
                    'closed_by' => null,
                    'status' => 'open',
                    'notes' => 'Shift opened for the day.',
                    'total_sales' => 0.00,
                    'transaction_count' => 0,
                ]);
            }
        }
    }
}
