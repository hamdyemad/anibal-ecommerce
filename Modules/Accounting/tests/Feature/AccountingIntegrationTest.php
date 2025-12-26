<?php

namespace Modules\Accounting\tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Modules\Accounting\app\Services\AccountingService;
use Modules\Accounting\app\Models\AccountingEntry;
use Modules\Accounting\app\Models\VendorBalance;

class AccountingIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private AccountingService $accountingService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->accountingService = new AccountingService();
        
        // Create minimal tables needed for testing
        Schema::create('accounting_entries', function ($table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->enum('type', ['income', 'expense', 'commission', 'refund']);
            $table->decimal('amount', 10, 2);
            $table->decimal('commission_rate', 5, 2)->nullable();
            $table->decimal('commission_amount', 10, 2)->nullable();
            $table->decimal('vendor_amount', 10, 2)->nullable();
            $table->string('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('vendor_balances', function ($table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id');
            $table->decimal('total_earnings', 10, 2)->default(0);
            $table->decimal('commission_deducted', 10, 2)->default(0);
            $table->decimal('available_balance', 10, 2)->default(0);
            $table->decimal('withdrawn_amount', 10, 2)->default(0);
            $table->unsignedBigInteger('country_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function test_accounting_summary_with_mixed_transactions()
    {
        // Create various types of entries
        AccountingEntry::create([
            'type' => 'income',
            'amount' => 500.00,
            'commission_amount' => 50.00,
            'vendor_id' => 1
        ]);

        AccountingEntry::create([
            'type' => 'income',
            'amount' => 300.00,
            'commission_amount' => 30.00,
            'vendor_id' => 2
        ]);

        AccountingEntry::create([
            'type' => 'expense',
            'amount' => 100.00,
            'vendor_id' => 1
        ]);

        AccountingEntry::create([
            'type' => 'refund',
            'amount' => -150.00,
            'vendor_id' => 1
        ]);

        $summary = $this->accountingService->getAccountingSummary();

        $this->assertEquals(800.00, $summary['total_income']);
        $this->assertEquals(100.00, $summary['total_expenses']);
        $this->assertEquals(80.00, $summary['total_commissions']);
        $this->assertEquals(150.00, $summary['total_refunds']);
        $this->assertEquals(700.00, $summary['net_profit']);
    }

    public function test_vendor_balance_operations()
    {
        // Test creating and updating vendor balance
        $balance = VendorBalance::create([
            'vendor_id' => 1,
            'total_earnings' => 100.00,
            'commission_deducted' => 10.00,
            'available_balance' => 90.00,
            'withdrawn_amount' => 0.00
        ]);

        // Test balance update
        $balance->updateBalance(50.00, 5.00);

        $this->assertEquals(150.00, $balance->total_earnings);
        $this->assertEquals(15.00, $balance->commission_deducted);
        $this->assertEquals(135.00, $balance->available_balance);
    }

    public function test_accounting_entry_scopes_work_together()
    {
        // Create entries of different types
        AccountingEntry::create(['type' => 'income', 'amount' => 100.00, 'vendor_id' => 1]);
        AccountingEntry::create(['type' => 'income', 'amount' => 200.00, 'vendor_id' => 1]);
        AccountingEntry::create(['type' => 'expense', 'amount' => 50.00, 'vendor_id' => 1]);
        AccountingEntry::create(['type' => 'commission', 'amount' => 20.00, 'vendor_id' => 1]);
        AccountingEntry::create(['type' => 'refund', 'amount' => -30.00, 'vendor_id' => 1]);

        // Test all scopes
        $this->assertCount(2, AccountingEntry::income()->get());
        $this->assertCount(1, AccountingEntry::expense()->get());
        $this->assertCount(1, AccountingEntry::commission()->get());
        $this->assertCount(1, AccountingEntry::refund()->get());

        // Test total amounts
        $this->assertEquals(300.00, AccountingEntry::income()->sum('amount'));
        $this->assertEquals(50.00, AccountingEntry::expense()->sum('amount'));
        $this->assertEquals(20.00, AccountingEntry::commission()->sum('amount'));
        $this->assertEquals(-30.00, AccountingEntry::refund()->sum('amount'));
    }
}
