<?php

namespace Database\Seeders;

use App\Models\Outlet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OutletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Outlet::create([
            'name' => 'Main Outlet',
            'address' => '123 Main St, Anytown, USA',
            'phone' => '555-1234',
        ]);

        Outlet::create([
            'name' => 'Second Outlet',
            'address' => '456 Second St, Anytown, USA',
            'phone' => '555-5678',
        ]);
    }
}
