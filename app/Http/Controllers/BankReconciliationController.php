<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\BankStatement;
use App\Models\BankStatementLine;
use App\Models\JournalEntry;
use App\Jobs\ProcessBankStatementJob;
use App\Services\BankReconciliationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BankReconciliationController extends Controller
{
    public function __construct(protected BankReconciliationService $service) {}

    /**
     * Dashboard showing all bank statement import batches.
     */
    public function index()
    {
        $statements = BankStatement::with('bankAccount', 'importer')
            ->latest()
            ->paginate(15);

        return view('bank-reconciliation.index', compact('statements'));
    }

    /**
     * Receive CSV upload, store it, dispatch background job.
     */
    public function import(Request $request)
    {
        $request->validate([
            'csv_file'          => 'required|file|mimes:csv,txt|max:51200', // 50 MB
            'bank_account_id'   => 'required|exists:accounts,id',
            'statement_date'    => 'required|date',
        ]);

        $file = $request->file('csv_file');
        $filename = uniqid('bank_') . '_' . now()->format('Ymd') . '.csv';
        $file->storeAs('bank-statements', $filename);

        $statement = BankStatement::create([
            'bank_account_id' => $request->bank_account_id,
            'imported_by'     => auth()->id(),
            'filename'        => $filename,
            'statement_date'  => $request->statement_date,
            'status'          => 'pending',
        ]);

        ProcessBankStatementJob::dispatch($statement);

        return redirect()->route('bank-recon.show', $statement)
            ->with('success', 'Bank statement uploaded. Background matching in progress...');
    }

    /**
     * Show statement lines with suggested/manual match UI.
     */
    public function show(BankStatement $bankStatement)
    {
        $lines = $bankStatement->lines()->with('reconciliation.journalEntry')->paginate(30);
        $suggestions = $this->service->suggestMatches($bankStatement);
        $bankAccounts = Account::where('code', 'LIKE', '11%')->get(); // Cash & Bank accounts

        return view('bank-reconciliation.show', compact('bankStatement', 'lines', 'suggestions', 'bankAccounts'));
    }

    /**
     * Confirm a match between a statement line and a GL journal entry.
     */
    public function confirmMatch(Request $request)
    {
        $request->validate([
            'statement_line_id' => 'required|exists:bank_statement_lines,id',
            'journal_entry_id'  => 'required|exists:journal_entries,id',
            'difference'        => 'nullable|numeric|min:0',
            'notes'             => 'nullable|string|max:500',
        ]);

        $line    = BankStatementLine::findOrFail($request->statement_line_id);
        $journal = JournalEntry::findOrFail($request->journal_entry_id);

        $this->service->confirmMatch(
            $line, $journal, auth()->id(),
            (float) $request->get('difference', 0),
            $request->notes
        );

        return back()->with('success', 'Reconciliation confirmed successfully.');
    }
}
