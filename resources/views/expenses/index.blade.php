<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Expenses Tracking') }}
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('expense-categories.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 font-bold rounded-xl border border-gray-200 dark:border-gray-700 hover:bg-gray-50 shadow-sm transition text-xs uppercase tracking-widest">
                    Manage Categories
                </a>
                <button onclick="openModal('addExpenseModal')" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white font-black rounded-xl hover:bg-indigo-700 shadow-md transition transform hover:-translate-y-0.5 text-xs uppercase tracking-widest">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Record Expense
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Filters --}}
            <div class="mb-8 bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 flex flex-wrap gap-4 items-end">
                <form action="{{ route('expenses.index') }}" method="GET" class="flex flex-wrap gap-4 items-end w-full">
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-[10px] font-black uppercase text-gray-400 tracking-[0.2em] mb-2">Category</label>
                        <select name="category_id" class="w-full rounded-xl border-gray-100 dark:bg-gray-700 dark:border-gray-600 text-sm">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-[10px] font-black uppercase text-gray-400 tracking-[0.2em] mb-2">Month</label>
                        <input type="date" name="month" value="{{ request('month') }}" onfocus="this.showPicker()" onkeydown="return false" class="w-full rounded-xl border-gray-100 dark:bg-gray-700 dark:border-gray-600 text-sm cursor-pointer" placeholder="Click to select month">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="px-6 py-2.5 bg-gray-900 dark:bg-indigo-600 text-white font-black rounded-xl text-xs uppercase tracking-widest">Filter</button>
                        <a href="{{ route('expenses.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-500 font-black rounded-xl text-xs uppercase tracking-widest">Reset</a>
                    </div>
                </form>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-6 py-4">Date</th>
                            <th class="px-6 py-4">Category</th>
                            <th class="px-6 py-4">Description</th>
                            <th class="px-6 py-4 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($expenses as $expense)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                                <td class="px-6 py-4 font-medium text-gray-500">{{ $expense->expense_date->format('M d, Y') }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded text-[10px] font-black uppercase tracking-wider">
                                        {{ $expense->category->name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $expense->description ?? '-' }}</td>
                                <td class="px-6 py-4 text-right font-black text-rose-600">
                                    ${{ number_format($expense->amount, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-400 italic bg-gray-50/30">No expenses found for this period.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-6 border-t border-gray-100 dark:border-gray-700">
                    {{ $expenses->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Add Expense Modal -->
    <div id="addExpenseModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-3xl p-8 max-w-lg w-full shadow-2xl">
            <h3 class="text-xl font-black text-gray-900 dark:text-white mb-6">Record New Expense</h3>
            <form action="{{ route('expenses.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-xs font-black uppercase text-gray-400 tracking-widest mb-1">Category</label>
                        <select name="category_id" required class="w-full rounded-xl border-gray-200 dark:bg-gray-700 dark:border-gray-600">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-black uppercase text-gray-400 tracking-widest mb-1">Amount</label>
                        <input type="number" step="0.01" name="amount" required class="w-full rounded-xl border-gray-200 dark:bg-gray-700 dark:border-gray-600">
                    </div>
                    <div>
                        <label class="block text-xs font-black uppercase text-gray-400 tracking-widest mb-1">Date</label>
                        <input type="date" name="expense_date" required value="{{ date('Y-m-d') }}" class="w-full rounded-xl border-gray-200 dark:bg-gray-700 dark:border-gray-600">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-black uppercase text-gray-400 tracking-widest mb-1">Description</label>
                        <textarea name="description" class="w-full rounded-xl border-gray-200 dark:bg-gray-700 dark:border-gray-600" rows="3"></textarea>
                    </div>
                </div>
                <div class="mt-8 flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('addExpenseModal')" class="px-6 py-2 text-gray-500 font-bold uppercase text-xs">Cancel</button>
                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white font-black rounded-xl uppercase text-xs shadow-md">Record Expense</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
        function closeModal(id) { document.getElementById(id).classList.add('hidden'); }
    </script>
</x-app-layout>
