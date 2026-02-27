<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Outlet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            [
                'name' => 'Ahmad Fazli',
                'email' => 'ahmad.fazli@example.com',
                'phone' => '0123456789',
                'points' => 1500,
            ],
            [
                'name' => 'Siti Nurhaliza',
                'email' => 'siti.nur@example.com',
                'phone' => '0198765432',
                'points' => 3200,
            ],
            [
                'name' => 'Wong Wei Kit',
                'email' => 'wong.wk@example.com',
                'phone' => '0112233445',
                'points' => 450,
            ],
            [
                'name' => 'Priya Sharma',
                'email' => 'priya.s@example.com',
                'phone' => '0165544332',
                'points' => 8500,
            ],
            [
                'name' => 'John Doe',
                'email' => 'john.d@example.com',
                'phone' => '0139988776',
                'points' => 0,
            ]
        ];

        foreach ($customers as $data) {
            Customer::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'loyalty_points' => $data['points'],
            ]);
        }
    }
}
