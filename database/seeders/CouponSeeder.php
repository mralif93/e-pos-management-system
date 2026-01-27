<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Coupon;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Coupon::create([
            'code' => 'SAVE10',
            'type' => 'percentage',
            'value' => 10,
            'is_active' => true,
        ]);

        Coupon::create([
            'code' => 'WELCOME5',
            'type' => 'fixed',
            'value' => 5.00,
            'min_spend' => 20.00,
            'is_active' => true,
        ]);

        $this->command->info('Coupons seeded: SAVE10 (10%), WELCOME5 ($5 off > $20)');
    }
}
