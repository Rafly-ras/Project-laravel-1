<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Product;
use App\Models\SalesOrder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

class FinancialModuleSeeder extends Seeder
{
    public function run(): array
    {
        // 1. Create Expense Categories
        $categories = [
            ['name' => 'Marketing', 'description' => 'Social media, ads, and offline marketing'],
            ['name' => 'Logistics', 'description' => 'Shipping, fuel, and warehouse maintenance'],
            ['name' => 'Operations', 'description' => 'Office supplies, utilities, and rent'],
            ['name' => 'Salaries', 'description' => 'Employee wages and bonuses'],
        ];

        foreach ($categories as $cat) {
            ExpenseCategory::firstOrCreate(['name' => $cat['name']], $cat);
        }

        // 2. Update Product Cost Prices (Randomly 60-80% of price)
        $products = Product::all();
        foreach ($products as $product) {
            $product->update([
                'cost_price' => $product->price * 0.7
            ]);
        }

        // 3. Create Sample Expenses for last 6 months
        $catIds = ExpenseCategory::pluck('id')->toArray();
        for ($i = 0; $i < 6; $i++) {
            $date = now()->subMonths($i);
            foreach ($catIds as $catId) {
                Expense::create([
                    'category_id' => $catId,
                    'amount' => rand(500, 2000),
                    'description' => "Monthly " . ExpenseCategory::find($catId)->name . " for " . $date->format('F'),
                    'expense_date' => $date->startOfMonth()->addDays(rand(0, 25)),
                    'created_by' => 1,
                ]);
            }
        }

        // 4. Backfill Profit for existing Sales Orders
        $salesOrders = SalesOrder::whereNotNull('confirmed_at')->get();
        foreach ($salesOrders as $so) {
            $totalProfit = 0;
            foreach ($so->items as $item) {
                $profitPerUnit = $item->price - $item->product->cost_price;
                $totalProfit += $profitPerUnit * $item->qty;
            }
            
            $so->update([
                'gross_profit' => $totalProfit,
                'margin_percentage' => $so->total_amount > 0 ? ($totalProfit / $so->total_amount) * 100 : 0
            ]);
        }

        return ['status' => 'success', 'message' => 'Financial data seeded successfully'];
    }
}
