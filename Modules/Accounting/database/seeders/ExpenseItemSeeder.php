<?php

namespace Modules\Accounting\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Accounting\app\Models\ExpenseItem;

class ExpenseItemSeeder extends Seeder
{
    public function run(): void
    {
        $expenseItems = [
            ['name' => 'Marketing & Advertising', 'description' => 'Marketing campaigns, ads, promotions'],
            ['name' => 'Server & Hosting', 'description' => 'Web hosting, cloud services, domain costs'],
            ['name' => 'Payment Processing', 'description' => 'Payment gateway fees, transaction costs'],
            ['name' => 'Office Supplies', 'description' => 'General office supplies and equipment'],
            ['name' => 'Software Licenses', 'description' => 'Software subscriptions and licenses'],
            ['name' => 'Customer Support', 'description' => 'Support tools, staff costs'],
            ['name' => 'Legal & Professional', 'description' => 'Legal fees, accounting, consulting'],
            ['name' => 'Utilities', 'description' => 'Internet, phone, electricity'],
        ];

        foreach ($expenseItems as $item) {
            ExpenseItem::create($item);
        }
    }
}
