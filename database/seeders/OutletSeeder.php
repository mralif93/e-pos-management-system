<?php

namespace Database\Seeders;

use App\Models\Outlet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OutletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $outlets = [
            [
                'name' => 'Cafe Delight',
                'address' => '123 Brew St, Coffee City',
                'phone' => '03-12345678',
                'settings' => [
                    'business_name' => 'Cafe Delight Sdn Bhd',
                    'business_registration_number' => '202401001111',
                    'tax_identification_number' => 'C-1111111111',
                    'business_address' => '123 Brew St, Coffee City',
                    'contact_email' => 'hello@cafedelight.com',
                    'invoice_prefix' => 'CAF/',
                    'receipt_header' => "Cafe Delight\nBrewing Happiness",
                    'receipt_footer' => "Follow us @cafedelight",
                    'pos_theme_color' => 'amber',
                ]
            ],
            [
                'name' => 'Fashion Boutique',
                'address' => '456 Style Ave, Fashion District',
                'phone' => '03-87654321',
                'settings' => [
                    'business_name' => 'Fashion Boutique Enterprise',
                    'business_registration_number' => '202401002222',
                    'tax_identification_number' => 'C-2222222222',
                    'business_address' => '456 Style Ave, Fashion District',
                    'contact_email' => 'style@boutique.com',
                    'invoice_prefix' => 'FAS/',
                    'receipt_header' => "Fashion Boutique\nTrendsetters Welcome",
                    'receipt_footer' => "No exchanges/refunds on sale items",
                    'pos_theme_color' => 'pink',
                ]
            ],
            [
                'name' => 'Green Mart',
                'address' => '789 Fresh Ln, Grocery Town',
                'phone' => '03-11223344',
                'settings' => [
                    'business_name' => 'Green Mart Grocer',
                    'business_registration_number' => '202401003333',
                    'tax_identification_number' => 'C-3333333333',
                    'business_address' => '789 Fresh Ln, Grocery Town',
                    'contact_email' => 'fresh@greenmart.com',
                    'invoice_prefix' => 'MRT/',
                    'receipt_header' => "Green Mart\nDaily Freshness",
                    'receipt_footer' => "Thank you for shopping with us",
                    'pos_theme_color' => 'green',
                ]
            ],
            [
                'name' => 'Tech Gadgets',
                'address' => '101 Silicon Rd, Tech Park',
                'phone' => '03-99887766',
                'settings' => [
                    'business_name' => 'Tech Gadgets Solutions',
                    'business_registration_number' => '202401004444',
                    'tax_identification_number' => 'C-4444444444',
                    'business_address' => '101 Silicon Rd, Tech Park',
                    'contact_email' => 'support@techgadgets.com',
                    'invoice_prefix' => 'TEC/',
                    'receipt_header' => "Tech Gadgets\nFuture is Here",
                    'receipt_footer' => "Warranty 1 Year on Electronics",
                    'pos_theme_color' => 'blue',
                ]
            ],
            [
                'name' => 'City Bookstore',
                'address' => '202 Read St, Book Haven',
                'phone' => '03-55443322',
                'settings' => [
                    'business_name' => 'City Bookstore Co.',
                    'business_registration_number' => '202401005555',
                    'tax_identification_number' => 'C-5555555555',
                    'business_address' => '202 Read St, Book Haven',
                    'contact_email' => 'read@citybookstore.com',
                    'invoice_prefix' => 'BKS/',
                    'receipt_header' => "City Bookstore\nRead More, Learn More",
                    'receipt_footer' => "Books are best friends",
                    'pos_theme_color' => 'indigo',
                ]
            ]
        ];

        foreach ($outlets as $data) {
            $settings = array_merge([
                'invoice_number_counter' => 1,
                'reset_counter_monthly' => true,
                'show_barcode' => true,
                'show_qr_code' => true,
                'default_tax_type' => 'SST',
                'currency_symbol' => 'RM',
                'tax_rate' => 6,
                'tax_inclusive_pricing' => true,
                'auto_submit_e_invoice' => true,
                'e_invoice_delay_minutes' => 5,
                'contact_phone' => $data['phone'],
                'show_outlet_name' => true,
                'show_cashier_name' => true,
                'myinvois_environment' => 'sandbox',
            ], $data['settings']);

            Outlet::create([
                'name' => $data['name'],
                'outlet_code' => Str::upper(Str::slug($data['name'])), // Generate code from name
                'address' => $data['address'],
                'phone' => $data['phone'],
                'is_active' => true,
                'has_pos_access' => true,
                'settings' => $settings,
            ]);
        }
    }
}
