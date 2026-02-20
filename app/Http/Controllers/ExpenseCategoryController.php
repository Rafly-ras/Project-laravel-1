<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use App\Http\Requests\StoreExpenseCategoryRequest;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    public function index()
    {
        $categories = ExpenseCategory::withCount('expenses')->get();
        return view('expenses.categories.index', compact('categories'));
    }

    public function store(StoreExpenseCategoryRequest $request)
    {
        ExpenseCategory::create($request->validated());
        return back()->with('success', 'Category created successfully.');
    }

    public function update(Request $request, ExpenseCategory $expenseCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:expense_categories,name,' . $expenseCategory->id,
            'description' => 'nullable|string',
        ]);

        $expenseCategory->update($validated);
        return back()->with('success', 'Category updated successfully.');
    }

    public function destroy(ExpenseCategory $expenseCategory)
    {
        if ($expenseCategory->expenses()->exists()) {
            return back()->with('error', 'Cannot delete category with existing expenses.');
        }
        $expenseCategory->delete();
        return back()->with('success', 'Category deleted successfully.');
    }
}
