
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\User;
use App\Models\Currency;
use App\Models\JournalEntry;
use App\Services\AccountingReportService;
use Illuminate\Support\Facades\Auth;

// Mock login
Auth::login(User::first());

$product = Product::first();
$warehouse = Warehouse::where('is_active', true)->first();
$currency = Currency::where('code', 'USD')->first() ?: Currency::first();

echo "1. Creating Sales Order...\n";
$so = SalesOrder::create([
    'sales_number' => 'SO-' . time(),
    'customer_name' => 'Verification Test',
    'status' => 'draft',
    'total_amount' => 1000,
    'currency_id' => $currency->id,
    'exchange_rate' => 1.0,
    'created_by' => Auth::id(),
]);

SalesOrderItem::create([
    'sales_order_id' => $so->id,
    'product_id' => $product->id,
    'warehouse_id' => $warehouse->id,
    'qty' => 1,
    'price' => 1000,
    'subtotal' => 1000,
]);

echo "2. Confirming Sales Order (Triggering Accounting Posting)...\n";
app(\App\Http\Controllers\SalesOrderController::class)->confirm($so);

$journal = JournalEntry::where('reference', $so->sales_number)->first();
if ($journal) {
    echo "SUCCESS: Journal Entry found for SO. Balanced: " . ($journal->isBalanced() ? 'YES' : 'NO') . "\n";
} else {
    echo "ERROR: Journal Entry NOT found for SO.\n";
}

echo "3. Running Trial Balance...\n";
$report = app(AccountingReportService::class)->getTrialBalance();
foreach ($report as $row) {
    if ($row['balance'] != 0) {
        echo "- Account: {$row['name']} ({$row['code']}) | Balance: {$row['balance']}\n";
    }
}

echo "\nVerification Script Finished.\n";
