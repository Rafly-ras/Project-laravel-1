<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Http\Requests\StoreExpenseRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with('category', 'creator');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('month')) {
            $query->whereMonth('expense_date', substr($request->month, 5, 2))
                  ->whereYear('expense_date', substr($request->month, 0, 4));
        }

        $expenses = $query->latest('expense_date')->paginate(15);
        $categories = ExpenseCategory::all();

        return view('expenses.index', compact('expenses', 'categories'));
    }

    public function store(StoreExpenseRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = Auth::id();

        Expense::create($data);

        return redirect()->route('expenses.index')->with('success', 'Expense recorded successfully.');
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
