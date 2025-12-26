<?php

namespace Modules\Accounting\tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Modules\Accounting\app\Services\AccountingService;
use Modules\Accounting\app\Models\AccountingEntry;

class AccountingServiceTest extends TestCase
{
    use RefreshDatabase;

    private AccountingService $accountingService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->accountingService = new AccountingService();
        
        // Create minimal table needed for testing
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
    }

    public function test_get_accounting_summary_returns_correct_structure()
    {
        // Create test data
        AccountingEntry::create([
            'type' => 'income',
            'amount' => 100.00,
            'commission_amount' => 10.00,
            'vendor_id' => 1
        ]);

        AccountingEntry::create([
            'type' => 'expense',
            'amount' => 30.00,
            'vendor_id' => 1
        ]);

        AccountingEntry::create([
            'type' => 'refund',
            'amount' => -20.00,
            'vendor_id' => 1
        ]);

        $summary = $this->accountingService->getAccountingSummary();

        $this->assertArrayHasKey('total_income', $summary);
        $this->assertArrayHasKey('total_expenses', $summary);
        $this->assertArrayHasKey('total_commissions', $summary);
        $this->assertArrayHasKey('total_refunds', $summary);
        $this->assertArrayHasKey('net_profit', $summary);

        $this->assertEquals(100.00, $summary['total_income']);
        $this->assertEquals(30.00, $summary['total_expenses']);
        $this->assertEquals(10.00, $summary['total_commissions']);
        $this->assertEquals(20.00, $summary['total_refunds']);
        $this->assertEquals(70.00, $summary['net_profit']);
    }

    public function test_get_accounting_summary_with_date_filters()
    {
        // Create entries with different dates
        AccountingEntry::create([
            'type' => 'income',
            'amount' => 100.00,
            'vendor_id' => 1,
            'created_at' => '2024-01-01'
        ]);

        AccountingEntry::create([
            'type' => 'income',
            'amount' => 200.00,
            'vendor_id' => 1,
            'created_at' => '2024-02-01'
        ]);

        $summary = $this->accountingService->getAccountingSummary([
            'date_from' => '2024-01-15',
            'date_to' => '2024-02-15'
        ]);

        $this->assertEquals(200.00, $summary['total_income']);
    }

    public function test_commission_rate_returns_default_value()
    {
        $reflection = new \ReflectionClass($this->accountingService);
        $method = $reflection->getMethod('getCommissionRate');
        $method->setAccessible(true);

        $rate = $method->invoke($this->accountingService, 1);
        
        $this->assertEquals(10.0, $rate);
    }
}
