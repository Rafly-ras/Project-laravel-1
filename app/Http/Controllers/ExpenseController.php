<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Currency;
use App\Http\Requests\StoreExpenseRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    protected $postingEngine;

    public function __construct(\App\Services\PostingEngine $postingEngine)
    {
        $this->postingEngine = $postingEngine;
    }

    public function index(Request $request)
    {
        $query = Expense::with('category', 'creator', 'currency');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('month')) {
            $query->whereMonth('expense_date', substr($request->month, 5, 2))
                  ->whereYear('expense_date', substr($request->month, 0, 4));
        }

        $expenses = $query->latest('expense_date')->paginate(15);
        $categories = ExpenseCategory::all();
        $currencies = Currency::where('is_active', true)->get();

        return view('expenses.index', compact('expenses', 'categories', 'currencies'));
    }

    public function store(StoreExpenseRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = Auth::id();

        return \Illuminate\Support\Facades\DB::transaction(function () use ($data) {
            $expense = \App\Models\Expense::create($data);

            // Post to Accounting
            $this->postingEngine->postExpense($expense);

            return redirect()->route('expenses.index')->with('success', 'Expense recorded successfully.');
        });
    }

    public function update(StoreExpenseRequest $request, Expense $expense)
    {
        $expense->update($request->validated());
        return redirect()->route('expenses.index')->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return redirect()->route('expenses.index')->with('success', 'Expense deleted successfully.');
    }
}
