<?php

namespace Modules\Accounting\tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Modules\Accounting\app\Models\AccountingEntry;

class AccountingEntryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
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
    }

    public function test_accounting_entry_can_be_created()
    {
        $entry = AccountingEntry::create([
            'order_id' => 1,
            'vendor_id' => 1,
            'type' => 'income',
            'amount' => 100.00,
            'commission_rate' => 10.00,
            'commission_amount' => 10.00,
            'vendor_amount' => 90.00,
            'description' => 'Test entry'
        ]);

        $this->assertInstanceOf(AccountingEntry::class, $entry);
        $this->assertEquals('income', $entry->type);
        $this->assertEquals(100.00, $entry->amount);
    }

    public function test_income_scope_filters_correctly()
    {
        AccountingEntry::create([
            'type' => 'income',
            'amount' => 100.00,
            'vendor_id' => 1
        ]);

        AccountingEntry::create([
            'type' => 'expense',
            'amount' => 50.00,
            'vendor_id' => 1
        ]);

        $incomeEntries = AccountingEntry::income()->get();
        
        $this->assertCount(1, $incomeEntries);
        $this->assertEquals('income', $incomeEntries->first()->type);
    }

    public function test_expense_scope_filters_correctly()
    {
        AccountingEntry::create([
            'type' => 'income',
            'amount' => 100.00,
            'vendor_id' => 1
        ]);

        AccountingEntry::create([
            'type' => 'expense',
            'amount' => 50.00,
            'vendor_id' => 1
        ]);

        $expenseEntries = AccountingEntry::expense()->get();
        
        $this->assertCount(1, $expenseEntries);
        $this->assertEquals('expense', $expenseEntries->first()->type);
    }

    public function test_commission_scope_filters_correctly()
    {
        AccountingEntry::create([
            'type' => 'commission',
            'amount' => 10.00,
            'vendor_id' => 1
        ]);

        AccountingEntry::create([
            'type' => 'income',
            'amount' => 100.00,
            'vendor_id' => 1
        ]);

        $commissionEntries = AccountingEntry::commission()->get();
        
        $this->assertCount(1, $commissionEntries);
        $this->assertEquals('commission', $commissionEntries->first()->type);
    }

    public function test_refund_scope_filters_correctly()
    {
        AccountingEntry::create([
            'type' => 'refund',
            'amount' => -100.00,
            'vendor_id' => 1
        ]);

        AccountingEntry::create([
            'type' => 'income',
            'amount' => 100.00,
            'vendor_id' => 1
        ]);

        $refundEntries = AccountingEntry::refund()->get();
        
        $this->assertCount(1, $refundEntries);
        $this->assertEquals('refund', $refundEntries->first()->type);
    }

    public function test_metadata_is_cast_to_array()
    {
        $metadata = ['order_number' => 'ORD-123', 'notes' => 'Test order'];
        
        $entry = AccountingEntry::create([
            'type' => 'income',
            'amount' => 100.00,
            'vendor_id' => 1,
            'metadata' => $metadata
        ]);

        $this->assertIsArray($entry->metadata);
        $this->assertEquals($metadata, $entry->metadata);
    }
}
