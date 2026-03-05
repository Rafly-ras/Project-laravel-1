<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Account;
use App\Models\AccountingPeriod;

class ChartOfAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Assets
        $assets = Account::firstOrCreate(['code' => '1000'], ['name' => 'Assets', 'type' => 'Asset']);
        Account::firstOrCreate(['code' => '1100'], ['name' => 'Cash & Bank', 'type' => 'Asset', 'parent_id' => $assets->id]);
        Account::firstOrCreate(['code' => '1200'], ['name' => 'Accounts Receivable', 'type' => 'Asset', 'parent_id' => $assets->id]);
        Account::firstOrCreate(['code' => '1300'], ['name' => 'Inventory', 'type' => 'Asset', 'parent_id' => $assets->id]);
        Account::firstOrCreate(['code' => '1400'], ['name' => 'Prepaid Expenses', 'type' => 'Asset', 'parent_id' => $assets->id]);

        // 2. Liabilities
        $liabilities = Account::firstOrCreate(['code' => '2000'], ['name' => 'Liabilities', 'type' => 'Liability']);
        Account::firstOrCreate(['code' => '2100'], ['name' => 'Accounts Payable', 'type' => 'Liability', 'parent_id' => $liabilities->id]);
        Account::firstOrCreate(['code' => '2200'], ['name' => 'Deferred Revenue', 'type' => 'Liability', 'parent_id' => $liabilities->id]);

        // 3. Equity
        $equity = Account::firstOrCreate(['code' => '3000'], ['name' => 'Equity', 'type' => 'Equity']);
        Account::firstOrCreate(['code' => '3100'], ['name' => 'Opening Balance Equity', 'type' => 'Equity', 'parent_id' => $equity->id]);
        Account::firstOrCreate(['code' => '3200'], ['name' => 'Retained Earnings', 'type' => 'Equity', 'parent_id' => $equity->id]);

        // 4. Revenue
        $revenue = Account::firstOrCreate(['code' => '4000'], ['name' => 'Revenue', 'type' => 'Revenue']);
        Account::firstOrCreate(['code' => '4100'], ['name' => 'Sales Revenue', 'type' => 'Revenue', 'parent_id' => $revenue->id]);

        // 5. Expenses
        $expenses = Account::firstOrCreate(['code' => '5000'], ['name' => 'Expenses', 'type' => 'Expense']);
        Account::firstOrCreate(['code' => '5100'], ['name' => 'Cost of Goods Sold', 'type' => 'Expense', 'parent_id' => $expenses->id]);
        Account::firstOrCreate(['code' => '5200'], ['name' => 'Operating Expenses', 'type' => 'Expense', 'parent_id' => $expenses->id]);

        // Create an initial current month period
        AccountingPeriod::firstOrCreate(
            ['name' => now()->format('F Y')],
            [
                'start_date' => now()->startOfMonth(),
                'end_date' => now()->endOfMonth(),
                'status' => 'Open'
            ]
        );
    }
}
