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
            'has_pos_access' => true,
            'settings' => [
                // Business Identity
                'business_name' => 'My POS Business Sdn Bhd',
                'business_registration_number' => '202401001234',
                'tax_identification_number' => 'C-1234567890',
                'business_address' => '123 Main St, Anytown, USA',
                'contact_email' => 'support@mainoutlet.com',
                'contact_phone' => '555-1234',

                // Invoice Format
                'invoice_prefix' => 'VP/',
                'invoice_number_counter' => 1,
                'reset_counter_monthly' => true,
                'show_barcode' => true,
                'show_qr_code' => true,

                // Tax & Compliance
                'default_tax_type' => 'SST',
                'currency_symbol' => 'RM',
                'tax_rate' => 6,
                'tax_inclusive_pricing' => true,
                'auto_submit_e_invoice' => true,
                'e_invoice_delay_minutes' => 5,

                // Receipt
                'receipt_header' => "Welcome to Main Outlet!\nOpen Daily 9am-9pm",
                'receipt_footer' => "Thank you for shopping!\nVisit us at example.com",
                'show_outlet_name' => true,
                'show_cashier_name' => true,

                // API
                'myinvois_environment' => 'sandbox',
            ]
        ]);

        Outlet::create([
            'name' => 'Second Outlet',
            'address' => '456 Second St, Anytown, USA',
            'phone' => '555-5678',
            'has_pos_access' => true,
            'settings' => [
                // Business Identity
                'business_name' => 'Second Store Enterprise',
                'business_registration_number' => '202401005678',
                'tax_identification_number' => 'C-0987654321',
                'business_address' => '456 Second St, Anytown, USA',
                'contact_email' => 'contact@secondoutlet.com',
                'contact_phone' => '555-5678',

                // Invoice Format
                'invoice_prefix' => 'SO/',
                'invoice_number_counter' => 1,
                'reset_counter_monthly' => true,
                'show_barcode' => true,
                'show_qr_code' => true,

                // Tax & Compliance
                'default_tax_type' => 'SST',
                'currency_symbol' => 'RM',
                'tax_rate' => 6,
                'tax_inclusive_pricing' => true,
                'auto_submit_e_invoice' => true,
                'e_invoice_delay_minutes' => 5,

                // Receipt
                'receipt_header' => "Second Outlet\nBest Deals in Town",
                'receipt_footer' => "See you again soon!",
                'show_outlet_name' => true,
                'show_cashier_name' => true,

                // Theme Customization
                'pos_theme_color' => 'green', // POS App -> Green for contrast

                // API
                'myinvois_environment' => 'sandbox',
            ]
        ]);
    }
}
