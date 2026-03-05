
use App\Models\User;
use App\Models\JournalEntry;
use App\Models\Account;
use App\Services\AccountingService;
use App\Services\LedgerHasher;
use App\Services\ReversalService;
use Illuminate\Support\Facades\Auth;

Auth::login(User::first());

$accounting = app(AccountingService::class);
$hasher = app(LedgerHasher::class);
$reversal = app(ReversalService::class);

$cash = Account::where('code', '1100')->first();
$sales = Account::where('code', '4100')->first();

echo "1. Creating Immutable Journal Entry...\n";
$entry = $accounting->createJournalEntry([
    'entry_date' => now(),
    'reference' => 'IMMUTABLE-001',
    'description' => 'Test immutable hashing',
    'currency_id' => 1,
    'exchange_rate' => 1.0,
], [
    ['account_id' => $cash->id, 'debit' => 100, 'credit' => 0, 'base_debit' => 100, 'base_credit' => 0],
    ['account_id' => $sales->id, 'debit' => 0, 'credit' => 100, 'base_debit' => 0, 'base_credit' => 100],
]);

echo "Entry Hash: " . $entry->hash . "\n";
echo "Previous Hash: " . $entry->previous_hash . "\n";

echo "\n2. Attempting to Update (Should Fail)...\n";
try {
    $entry->update(['description' => '篡改 (Tamper)']);
    echo "ERROR: Update succeeded! Immutability broken.\n";
} catch (\LogicException $e) {
    echo "SUCCESS: Update blocked with: " . $e->getMessage() . "\n";
}

echo "\n3. Reversing the Entry...\n";
$rev = $reversal->reverse($entry, "Testing reversal workflow");
echo "Reversal Reference: " . $rev->reference . "\n";
echo "Reversal Hash: " . $rev->hash . "\n";

echo "\n4. Verifying Ledger Integrity...\n";
$audit = $hasher->verifyChain();
echo "Ledger Valid: " . ($audit['is_valid'] ? 'YES' : 'NO') . "\n";
if (!$audit['is_valid']) {
    print_r($audit['errors']);
}

echo "\nVerification Finished.\n";
