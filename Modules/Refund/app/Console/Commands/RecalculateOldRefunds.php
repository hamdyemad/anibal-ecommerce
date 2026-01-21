<?php

namespace Modules\Refund\app\Console\Commands;

use Illuminate\Console\Command;
use Modules\Refund\app\Models\RefundRequest;

class RecalculateOldRefunds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'refunds:recalculate-old';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate old refund requests that were created with wrong tax calculations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to recalculate old refund requests...');
        
        $refunds = RefundRequest::with('items.orderProduct.taxes')->get();
        $count = 0;
        
        foreach ($refunds as $refund) {
            $this->info("Processing refund #{$refund->refund_number}...");
            
            try {
                $refund->recalculateOldItems();
                $count++;
                $this->info("✓ Refund #{$refund->refund_number} recalculated successfully");
            } catch (\Exception $e) {
                $this->error("✗ Failed to recalculate refund #{$refund->refund_number}: {$e->getMessage()}");
            }
        }
        
        $this->info("\nRecalculation completed!");
        $this->info("Total refunds processed: {$count}");
        
        return 0;
    }
}
