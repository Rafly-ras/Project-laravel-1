<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Inventory Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Quick Stats --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Products -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6 border border-gray-100 dark:border-gray-700 hover:shadow-md transition">
                    <div class="flex items-center">
                        <div class="p-3 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl mr-4">
                            <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">Total Products</p>
                            <h3 class="text-2xl font-black text-gray-900 dark:text-white">{{ number_format($totalProducts) }}</h3>
                        </div>
                    </div>
                </div>

                <!-- Total Categories -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6 border border-gray-100 dark:border-gray-700 hover:shadow-md transition">
                    <div class="flex items-center">
                        <div class="p-3 bg-emerald-50 dark:bg-emerald-900/30 rounded-xl mr-4">
                            <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">Categories</p>
                            <h3 class="text-2xl font-black text-gray-900 dark:text-white">{{ number_format($totalCategories) }}</h3>
                        </div>
                    </div>
                </div>

                <!-- Total Value -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6 border border-gray-100 dark:border-gray-700 hover:shadow-md transition">
                    <div class="flex items-center">
                        <div class="p-3 bg-amber-50 dark:bg-amber-900/30 rounded-xl mr-4">
                            <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">Stock Value</p>
                            <h3 class="text-2xl font-black text-gray-900 dark:text-white">${{ number_format($totalValue, 2) }}</h3>
                        </div>
                    </div>
                </div>

                <!-- Low Stock Alert -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6 border border-gray-100 dark:border-gray-700 hover:shadow-md transition">
                    <div class="flex items-center">
                        <div class="p-3 bg-rose-50 dark:bg-rose-900/30 rounded-xl mr-4">
                            <svg class="w-6 h-6 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">Low Stock</p>
                            <h3 class="text-2xl font-black text-gray-900 dark:text-white">{{ $lowStockCount }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Shortcut Buttons --}}
            <div class="flex flex-wrap gap-4 mb-10">
                @can('manage-products')
                    <a href="{{ route('products.index') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 shadow-sm hover:shadow-md transition group">
                        <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        Manage Products
                    </a>
                @endcan

                @can('manage-transactions')
                    <a href="{{ route('transactions.index') }}" class="inline-flex items-center px-6 py-3 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 font-bold rounded-xl border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 shadow-sm hover:shadow-md transition group">
                        <svg class="w-5 h-5 mr-2 group-hover:rotate-12 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        Transactions
                    </a>
                @endcan

                @can('view-reports')
                    <a href="{{ route('products.stock-summary') }}" class="inline-flex items-center px-6 py-3 bg-white dark:bg-gray-800 text-emerald-600 dark:text-emerald-400 font-bold rounded-xl border border-emerald-100 dark:border-emerald-900/30 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 shadow-sm hover:shadow-md transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Stock Summary
                    </a>
                @endcan

                @can('manage-transactions')
                    <a href="{{ route('transactions.create') }}" class="inline-flex items-center px-6 py-3 bg-white dark:bg-gray-800 text-indigo-600 dark:text-indigo-400 font-bold rounded-xl border border-indigo-100 dark:border-indigo-900/30 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 shadow-sm hover:shadow-md transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        Record Movement
                    </a>
                @endcan
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                {{-- Low Stock Alerts --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden border border-gray-100 dark:border-gray-700">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-700/30">
                        <h3 class="text-lg font-black text-gray-900 dark:text-white flex items-center">
                            <span class="w-2 h-2 bg-rose-500 rounded-full mr-2 animate-pulse"></span>
                            Low Stock Alerts
                        </h3>
                        <span class="px-2 py-0.5 bg-rose-100 text-rose-800 text-xs font-bold rounded-full">{{ $lowStockCount }} Items</span>
                    </div>
                    <div class="p-0">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-6 py-4">Product</th>
                                    <th class="px-6 py-4">Stock</th>
                                    <th class="px-6 py-4 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse($lowStockProducts as $product)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-gray-900 dark:text-white">{{ $product->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $product->category->name ?? 'Uncategorized' }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="font-black text-rose-600">{{ $product->stock }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            @can('manage-transactions')
                                                <a href="{{ route('products.transactions.create', $product) }}" class="text-indigo-600 hover:underline font-bold text-xs uppercase tracking-wider">Restock</a>
                                            @else
                                                <span class="text-gray-400 text-xs uppercase tracking-wider italic">View Only</span>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-10 text-center text-emerald-600 font-bold bg-emerald-50/30 dark:bg-emerald-900/10">
                                            <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            All products are well stocked!
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Recent Transactions --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden border border-gray-100 dark:border-gray-700">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-700/30">
                        <h3 class="text-lg font-black text-gray-900 dark:text-white">Recent Transactions</h3>
                        <a href="{{ route('transactions.index') }}" class="text-xs text-indigo-600 hover:underline font-bold uppercase transition">View All</a>
                    </div>
                    <div class="p-0">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-6 py-4">Product</th>
                                    <th class="px-6 py-4 text-center">Qty</th>
                                    <th class="px-6 py-4 text-right">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse($recentTransactions as $transaction)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-gray-900 dark:text-white">{{ $transaction->product->name }}</div>
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-black {{ $transaction->type === 'Masuk' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-400' }}">
                                                {{ strtoupper($transaction->type) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="font-bold {{ $transaction->type === 'Masuk' ? 'text-emerald-600' : 'text-rose-600' }}">
                                                {{ $transaction->type === 'Masuk' ? '+' : '-' }}{{ $transaction->quantity }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right text-xs text-gray-500">
                                            {{ $transaction->created_at->diffForHumans() }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-10 text-center text-gray-400 font-medium">No transactions yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
