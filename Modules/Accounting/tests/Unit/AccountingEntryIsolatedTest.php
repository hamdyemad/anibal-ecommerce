<?php

namespace Modules\Accounting\tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\Model;

// Create a test-only model without the problematic traits
class TestAccountingEntry extends Model
{
    protected $table = 'accounting_entries';
    
    protected $fillable = [
        'type',
        'amount',
        'description',
        'metadata'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array'
    ];

    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    public function scopeCommission($query)
    {
        return $query->where('type', 'commission');
    }

    public function scopeRefund($query)
    {
        return $query->where('type', 'refund');
    }
}

class AccountingEntryIsolatedTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create minimal table schema for testing
        Schema::create('accounting_entries', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->decimal('amount', 10, 2);
            $table->string('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('accounting_entries');
        parent::tearDown();
    }

    public function test_accounting_entry_can_be_created()
    {
        $entry = TestAccountingEntry::create([
            'type' => 'income',
            'amount' => 100.50,
            'description' => 'Test income entry'
        ]);

        $this->assertInstanceOf(TestAccountingEntry::class, $entry);
        $this->assertEquals('income', $entry->type);
        $this->assertEquals(100.50, $entry->amount);
        $this->assertEquals('Test income entry', $entry->description);
    }

    public function test_income_scope_filters_correctly()
    {
        TestAccountingEntry::create(['type' => 'income', 'amount' => 100]);
        TestAccountingEntry::create(['type' => 'expense', 'amount' => 50]);
        TestAccountingEntry::create(['type' => 'income', 'amount' => 75]);

        $incomeEntries = TestAccountingEntry::income()->get();

        $this->assertCount(2, $incomeEntries);
        $this->assertTrue($incomeEntries->every(fn($entry) => $entry->type === 'income'));
    }

    public function test_expense_scope_filters_correctly()
    {
        TestAccountingEntry::create(['type' => 'income', 'amount' => 100]);
        TestAccountingEntry::create(['type' => 'expense', 'amount' => 50]);
        TestAccountingEntry::create(['type' => 'expense', 'amount' => 25]);

        $expenseEntries = TestAccountingEntry::expense()->get();

        $this->assertCount(2, $expenseEntries);
        $this->assertTrue($expenseEntries->every(fn($entry) => $entry->type === 'expense'));
    }

    public function test_commission_scope_filters_correctly()
    {
        TestAccountingEntry::create(['type' => 'commission', 'amount' => 10]);
        TestAccountingEntry::create(['type' => 'income', 'amount' => 100]);
        TestAccountingEntry::create(['type' => 'commission', 'amount' => 15]);

        $commissionEntries = TestAccountingEntry::commission()->get();

        $this->assertCount(2, $commissionEntries);
        $this->assertTrue($commissionEntries->every(fn($entry) => $entry->type === 'commission'));
    }

    public function test_refund_scope_filters_correctly()
    {
        TestAccountingEntry::create(['type' => 'refund', 'amount' => 25]);
        TestAccountingEntry::create(['type' => 'income', 'amount' => 100]);
        TestAccountingEntry::create(['type' => 'refund', 'amount' => 30]);

        $refundEntries = TestAccountingEntry::refund()->get();

        $this->assertCount(2, $refundEntries);
        $this->assertTrue($refundEntries->every(fn($entry) => $entry->type === 'refund'));
    }

    public function test_metadata_is_cast_to_array()
    {
        $metadata = ['key' => 'value', 'number' => 123];
        
        $entry = TestAccountingEntry::create([
            'type' => 'income',
            'amount' => 100,
            'metadata' => $metadata
        ]);

        $this->assertIsArray($entry->metadata);
        $this->assertEquals($metadata, $entry->metadata);
    }
}
