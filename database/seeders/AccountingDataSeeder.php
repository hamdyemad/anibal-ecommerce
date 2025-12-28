<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Order\app\Models\Order;
use Modules\Order\app\Models\OrderProduct;
use Modules\Order\app\Models\OrderStage;
use Modules\Accounting\app\Models\AccountingEntry;
use Modules\Accounting\app\Models\VendorBalance;
use Modules\Vendor\app\Models\Vendor;

class AccountingDataSeeder extends Seeder
{
    /**
     * Seed accounting data from existing delivered orders.
     */
    public function run(): void
    {
        $this->command->info('Starting Accounting Data Seeder...');
        
        // Get delivered stage
        $deliveredStage = OrderStage::withoutGlobalScopes()->where('type', 'deliver')->first();
        
        if (!$deliveredStage) {
            $this->command->error('No delivered stage found! Please create an order stage with type "deliver" first.');
            return;
        }
        
        $this->command->info("Found delivered stage: {$deliveredStage->name} (ID: {$deliveredStage->id})");
        
        // Get all delivered orders
        $deliveredOrders = Order::withoutGlobalScopes()
            ->where('stage_id', $deliveredStage->id)
            ->with(['products'])
            ->get();
        
        $this->command->info("Found {$deliveredOrders->count()} delivered orders");
        
        if ($deliveredOrders->isEmpty()) {
            $this->command->warn('No delivered orders found to process.');
            return;
        }
        
        $accountingEntriesCreated = 0;
        $vendorBalancesUpdated = 0;
        
        // Process each delivered order
        foreach ($deliveredOrders as $order) {
            $this->command->info("Processing Order #{$order->order_number}...");
            
            // Group order products by vendor
            $vendorProducts = $order->products->groupBy('vendor_id');
            
            foreach ($vendorProducts as $vendorId => $products) {
                if (!$vendorId) {
                    $this->command->warn("  - Skipping products without vendor_id");
                    continue;
                }
                
                // Calculate totals for this vendor in this order
                $totalAmount = $products->sum(function($p) {
                    return $p->price * $p->quantity;
                });
                $totalCommission = $products->sum('commission');
                $vendorAmount = $totalAmount - $totalCommission;
                
                // Check if accounting entry already exists
                $existingEntry = AccountingEntry::withoutGlobalScopes()
                    ->where('order_id', $order->id)
                    ->where('vendor_id', $vendorId)
                    ->where('type', 'income')
                    ->first();
                
                if (!$existingEntry) {
                    // Create accounting entry
                    $entry = new AccountingEntry();
                    $entry->order_id = $order->id;
                    $entry->vendor_id = $vendorId;
                    $entry->type = 'income';
                    $entry->amount = $totalAmount;
                    $entry->commission_rate = $totalAmount > 0 ? ($totalCommission / $totalAmount) * 100 : 0;
                    $entry->commission_amount = $totalCommission;
                    $entry->vendor_amount = $vendorAmount;
                    $entry->description = "Income from Order #{$order->order_number}";
                    $entry->country_id = $order->country_id ?? null;
                    $entry->save();
                    
                    // Update timestamps to match order
                    DB::table('accounting_entries')
                        ->where('id', $entry->id)
                        ->update([
                            'created_at' => $order->getRawOriginal('created_at'),
                            'updated_at' => $order->getRawOriginal('updated_at'),
                        ]);
                    
                    $accountingEntriesCreated++;
                    $this->command->info("  - Created AccountingEntry for Vendor ID: {$vendorId} (Amount: {$totalAmount}, Commission: {$totalCommission})");
                } else {
                    $this->command->info("  - AccountingEntry already exists for Vendor ID: {$vendorId}");
                }
                
                // Update or create vendor balance
                $vendorBalance = VendorBalance::withoutGlobalScopes()
                    ->where('vendor_id', $vendorId)
                    ->first();
                
                if (!$vendorBalance) {
                    // Calculate total from all delivered orders for this vendor
                    $allVendorProducts = OrderProduct::withoutGlobalScopes()
                        ->where('vendor_id', $vendorId)
                        ->whereHas('order', function($q) use ($deliveredStage) {
                            $q->withoutGlobalScopes()->where('stage_id', $deliveredStage->id);
                        })
                        ->get();
                    
                    $totalEarnings = $allVendorProducts->sum(function($p) {
                        return $p->price * $p->quantity;
                    });
                    $totalCommissionDeducted = $allVendorProducts->sum('commission');
                    $availableBalance = $totalEarnings - $totalCommissionDeducted;
                    
                    // Get vendor's country_id
                    $vendor = Vendor::withoutGlobalScopes()->find($vendorId);
                    
                    VendorBalance::create([
                        'vendor_id' => $vendorId,
                        'total_earnings' => $totalEarnings,
                        'commission_deducted' => $totalCommissionDeducted,
                        'available_balance' => $availableBalance,
                        'withdrawn_amount' => 0,
                        'country_id' => $vendor->country_id ?? null,
                    ]);
                    $vendorBalancesUpdated++;
                    $this->command->info("  - Created VendorBalance for Vendor ID: {$vendorId} (Earnings: {$totalEarnings}, Available: {$availableBalance})");
                }
            }
        }
        
        // Now recalculate all vendor balances to ensure accuracy
        $this->command->info("\nRecalculating all vendor balances...");
        
        $allVendors = Vendor::withoutGlobalScopes()->get();
        
        foreach ($allVendors as $vendor) {
            // Get all delivered order products for this vendor
            $vendorOrderProducts = OrderProduct::withoutGlobalScopes()
                ->where('vendor_id', $vendor->id)
                ->whereHas('order', function($q) use ($deliveredStage) {
                    $q->withoutGlobalScopes()->where('stage_id', $deliveredStage->id);
                })
                ->get();
            
            if ($vendorOrderProducts->isEmpty()) {
                continue;
            }
            
            $totalEarnings = $vendorOrderProducts->sum(function($p) {
                return $p->price * $p->quantity;
            });
            $totalCommissionDeducted = $vendorOrderProducts->sum('commission');
            
            // Get existing withdrawn amount
            $vendorBalance = VendorBalance::withoutGlobalScopes()
                ->where('vendor_id', $vendor->id)
                ->first();
            
            $withdrawnAmount = $vendorBalance ? $vendorBalance->withdrawn_amount : 0;
            $availableBalance = $totalEarnings - $totalCommissionDeducted - $withdrawnAmount;
            
            if ($vendorBalance) {
                $vendorBalance->update([
                    'total_earnings' => $totalEarnings,
                    'commission_deducted' => $totalCommissionDeducted,
                    'available_balance' => $availableBalance,
                ]);
            } else {
                VendorBalance::create([
                    'vendor_id' => $vendor->id,
                    'total_earnings' => $totalEarnings,
                    'commission_deducted' => $totalCommissionDeducted,
                    'available_balance' => $availableBalance,
                    'withdrawn_amount' => 0,
                    'country_id' => $vendor->country_id ?? null,
                ]);
                $vendorBalancesUpdated++;
            }
            
            $this->command->info("  - Updated balance for Vendor: {$vendor->name} (Earnings: {$totalEarnings}, Commission: {$totalCommissionDeducted}, Available: {$availableBalance})");
        }
        
        $this->command->info("\n========================================");
        $this->command->info("Accounting Data Seeder Complete!");
        $this->command->info("AccountingEntries created: {$accountingEntriesCreated}");
        $this->command->info("VendorBalances updated: {$vendorBalancesUpdated}");
        $this->command->info("========================================");
    }
}
