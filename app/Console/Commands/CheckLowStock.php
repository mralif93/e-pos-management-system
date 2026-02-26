<?php

namespace App\Console\Commands;

use App\Services\LowStockAlertService;
use Illuminate\Console\Command;

class CheckLowStock extends Command
{
    protected $signature = 'inventory:check-low-stock {--outlet= : Specific outlet ID to check}';

    protected $description = 'Check and create low stock alerts';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(LowStockAlertService $alertService): int
    {
        $outletId = $this->option('outlet');

        if ($outletId) {
            $this->info("Checking low stock for outlet {$outletId}...");
            $alerts = $alertService->checkOutletStock((int) $outletId);
        } else {
            $this->info('Checking global low stock...');
            $alerts = $alertService->checkGlobalStock();
        }

        if ($alerts->isEmpty()) {
            $this->info('No low stock alerts created.');
            return Command::SUCCESS;
        }

        $this->warn("Created {$alerts->count()} low stock alert(s).");
        
        $bar = $this->output->createProgressBar($alerts->count());
        $bar->start();

        foreach ($alerts as $alert) {
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        return Command::SUCCESS;
    }
}
