<?php

namespace Modules\Accounting\tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Modules\Accounting\app\Models\VendorBalance;

class VendorBalanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create minimal table needed for testing
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

    public function test_vendor_balance_can_be_created()
    {
        $balance = VendorBalance::create([
            'vendor_id' => 1,
            'total_earnings' => 100.00,
            'commission_deducted' => 10.00,
            'available_balance' => 90.00,
            'withdrawn_amount' => 0.00
        ]);

        $this->assertInstanceOf(VendorBalance::class, $balance);
        $this->assertEquals(100.00, $balance->total_earnings);
        $this->assertEquals(90.00, $balance->available_balance);
    }

    public function test_update_balance_increases_earnings()
    {
        $balance = VendorBalance::create([
            'vendor_id' => 1,
            'total_earnings' => 100.00,
            'commission_deducted' => 10.00,
            'available_balance' => 90.00,
            'withdrawn_amount' => 0.00
        ]);

        $balance->updateBalance(50.00, 5.00);

        $this->assertEquals(150.00, $balance->total_earnings);
        $this->assertEquals(15.00, $balance->commission_deducted);
        $this->assertEquals(135.00, $balance->available_balance);
    }

    public function test_update_balance_handles_negative_amounts()
    {
        $balance = VendorBalance::create([
            'vendor_id' => 1,
            'total_earnings' => 100.00,
            'commission_deducted' => 10.00,
            'available_balance' => 90.00,
            'withdrawn_amount' => 0.00
        ]);

        $balance->updateBalance(-30.00, -3.00);

        $this->assertEquals(70.00, $balance->total_earnings);
        $this->assertEquals(7.00, $balance->commission_deducted);
        $this->assertEquals(63.00, $balance->available_balance);
    }

    public function test_available_balance_calculation_with_withdrawals()
    {
        $balance = VendorBalance::create([
            'vendor_id' => 1,
            'total_earnings' => 100.00,
            'commission_deducted' => 10.00,
            'available_balance' => 90.00,
            'withdrawn_amount' => 20.00
        ]);

        $balance->updateBalance(50.00, 5.00);

        // available_balance = total_earnings - commission_deducted - withdrawn_amount
        // 150.00 - 15.00 - 20.00 = 115.00
        $this->assertEquals(115.00, $balance->available_balance);
    }
}
