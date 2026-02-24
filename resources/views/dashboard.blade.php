<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-saas-navy dark:text-gray-200 leading-tight">
                {{ __('Inventory Dashboard') }}
            </h2>
            <a href="{{ route('reports.master-o2c') }}" class="inline-flex items-center px-4 py-2 bg-saas-blue text-white font-semibold rounded-saas hover:bg-blue-700 shadow-saas transition text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Master O2C Report
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-saas-bg dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Warehouse Filter --}}
            <div class="mb-8 flex justify-end">
                <form action="{{ route('dashboard') }}" method="GET" class="flex items-center space-x-2">
                    <label for="warehouse_id" class="text-xs font-semibold text-saas-slate uppercase tracking-wider">Filter by Warehouse:</label>
                    <select name="warehouse_id" id="warehouse_id" onchange="this.form.submit()" class="text-sm rounded-saas border-saas-border dark:border-gray-700 dark:bg-gray-800 shadow-saas focus:ring-saas-blue focus:border-saas-blue py-2 pr-10">
                        <option value="">All Warehouses</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" {{ $warehouseId == $warehouse->id ? 'selected' : '' }}>
                                {{ $warehouse->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            <!-- Financial Engine -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
                {{-- Total Revenue --}}
                <div class="bg-white dark:bg-gray-800 rounded-saas p-6 shadow-saas border border-saas-border dark:border-gray-700 relative overflow-hidden group">
                    <div class="relative z-10">
                        <p class="text-xs font-semibold text-saas-slate uppercase tracking-wider mb-2">Total Revenue</p>
                        <h3 class="text-3xl font-bold text-saas-blue">${{ number_format($totalRevenue, 2) }}</h3>
                        <div class="mt-4 flex items-center text-[10px] font-semibold text-saas-blue bg-saas-soft w-fit px-3 py-1 rounded-full uppercase tracking-wider">
                            Confirmed Sales
                        </div>
                    </div>
                </div>

                {{-- Gross Profit --}}
                <div class="bg-white dark:bg-gray-800 rounded-saas p-6 shadow-saas border border-saas-border dark:border-gray-700 relative overflow-hidden group">
                    <div class="relative z-10">
                        <div class="flex justify-between items-start">
                            <p class="text-xs font-semibold text-saas-slate uppercase tracking-wider mb-2">Gross Profit</p>
                            <span class="text-[10px] font-bold text-saas-success bg-green-50 px-2 py-0.5 rounded-lg border border-green-100">{{ number_format($marginPercentage, 1) }}% Margin</span>
                        </div>
                        <h3 class="text-3xl font-bold text-saas-navy dark:text-white">${{ number_format($totalGrossProfit, 2) }}</h3>
                        <div class="mt-4 flex items-center text-[10px] font-semibold text-saas-slate bg-gray-50 w-fit px-3 py-1 rounded-full uppercase tracking-wider">
                            Revenue - COGS
                        </div>
                    </div>
                </div>

                {{-- Total Expenses --}}
                <div class="bg-white dark:bg-gray-800 rounded-saas p-6 shadow-saas border border-saas-border dark:border-gray-700 relative overflow-hidden group">
                    <div class="relative z-10">
                        <p class="text-xs font-semibold text-saas-slate uppercase tracking-wider mb-2">Total Expenses</p>
                        <h3 class="text-3xl font-bold text-saas-navy dark:text-white">${{ number_format($totalExpenses, 2) }}</h3>
                        <a href="{{ route('expenses.index') }}" class="mt-4 flex items-center text-[10px] font-semibold text-saas-danger bg-red-50 hover:bg-red-100 transition w-fit px-3 py-1 rounded-full uppercase tracking-wider">
                            Manage Expenses →
                        </a>
                    </div>
                </div>

                {{-- Net Profit / Cashflow --}}
                <div class="bg-white dark:bg-gray-800 rounded-saas p-6 shadow-saas border border-saas-border dark:border-gray-700 relative overflow-hidden group">
                    <div class="relative z-10">
                        <p class="text-xs font-semibold text-saas-slate uppercase tracking-wider mb-2">Net Cashflow</p>
                        <h3 class="text-3xl font-bold {{ $netProfit >= 0 ? 'text-saas-blue' : 'text-saas-danger' }}">
                            ${{ number_format($netProfit, 2) }}
                        </h3>
                        <div class="mt-4 flex items-center text-[10px] font-bold {{ $netProfit >= 0 ? 'text-saas-blue bg-saas-soft' : 'text-saas-danger bg-red-50' }} dark:bg-gray-700 uppercase tracking-widest w-fit px-3 py-1 rounded-full">
                            {{ $netProfit >= 0 ? 'Surplus' : 'Deficit' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Liquidity Intelligence -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
                <div class="bg-white dark:bg-gray-800 rounded-saas p-6 border border-saas-border dark:border-gray-700 relative group overflow-hidden shadow-saas">
                    <p class="text-xs font-semibold uppercase text-saas-blue tracking-wider mb-2">Monthly Cash In</p>
                    <h3 class="text-3xl font-bold text-saas-navy dark:text-white">${{ number_format($currentMonthStats['revenue'], 2) }}</h3>
                    <div class="mt-4 flex items-center text-[10px] text-saas-slate font-semibold uppercase tracking-widest">
                         {{ now()->format('F') }} Liquidity
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-saas p-6 border border-saas-border dark:border-gray-700 relative group overflow-hidden shadow-saas">
                    <p class="text-xs font-semibold uppercase text-saas-slate dark:text-gray-500 tracking-wider mb-2">Outstanding Recv.</p>
                    <h3 class="text-3xl font-bold text-saas-navy dark:text-white">${{ number_format($outstandingReceivables, 2) }}</h3>
                    <div class="mt-4 flex items-center text-[10px] text-saas-success font-semibold uppercase tracking-widest">
                        Receivable Assets
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-saas p-6 border border-saas-border dark:border-gray-700 relative group overflow-hidden shadow-saas">
                    <p class="text-xs font-semibold uppercase text-saas-slate dark:text-gray-500 tracking-wider mb-2">Monthly Expenses</p>
                    <h3 class="text-3xl font-bold text-saas-navy dark:text-white">${{ number_format($currentMonthStats['expense'], 2) }}</h3>
                    <div class="mt-4 flex items-center text-[10px] text-saas-danger font-semibold uppercase tracking-widest">
                        Total Outflow
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-saas p-6 border border-saas-border dark:border-gray-700 relative group overflow-hidden shadow-saas">
                    <p class="text-xs font-semibold uppercase text-saas-blue tracking-wider mb-2">Monthly Net Result</p>
                    <h3 class="text-3xl font-bold text-saas-navy dark:text-white">${{ number_format($currentMonthStats['net'], 2) }}</h3>
                    <div class="mt-4 flex items-center text-[10px] {{ $currentMonthStats['net'] >= 0 ? 'text-saas-success' : 'text-saas-danger' }} font-semibold uppercase tracking-widest">
                        {{ $currentMonthStats['net'] >= 0 ? 'Surplus' : 'Deficit' }}
                    </div>
                </div>
            </div>

            <!-- Profit Intelligence -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
                <div class="bg-white dark:bg-gray-800 rounded-saas p-6 shadow-saas border border-saas-border dark:border-gray-700 relative overflow-hidden">
                    <p class="text-xs font-semibold uppercase text-saas-blue tracking-wider mb-2">{{ now()->format('F') }} Net Profit</p>
                    <h3 class="text-3xl font-bold text-saas-navy dark:text-white">${{ number_format($monthProfit['net_profit'], 2) }}</h3>
                    <div class="mt-4 flex items-center text-[10px] bg-saas-soft text-saas-blue w-fit px-3 py-1 rounded-full font-semibold uppercase tracking-wider">
                         Accrual Performance
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-saas p-6 border border-saas-border dark:border-gray-700 relative overflow-hidden shadow-saas">
                    <p class="text-xs font-semibold uppercase text-saas-slate dark:text-gray-500 tracking-wider mb-2">Profit Margin %</p>
                    <h3 class="text-3xl font-bold text-saas-navy dark:text-white">{{ number_format($monthProfit['margin_percentage'], 1) }}%</h3>
                    <div class="mt-4 flex items-center text-[10px] text-saas-success font-semibold uppercase tracking-widest">
                        Earnings Efficiency
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-saas p-6 border border-saas-border dark:border-gray-700 relative overflow-hidden shadow-saas">
                    <p class="text-xs font-semibold uppercase text-saas-slate dark:text-gray-500 tracking-wider mb-2">Best Performing Product</p>
                    <h3 class="text-xl font-bold text-saas-navy dark:text-white truncate">{{ $topProducts->first()?->product->name ?? 'N/A' }}</h3>
                    <div class="mt-4 flex items-center text-[10px] text-saas-blue font-semibold uppercase tracking-widest">
                        Top Contributor
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-saas p-6 border border-saas-border dark:border-gray-700 relative overflow-hidden shadow-saas">
                    <p class="text-xs font-semibold uppercase text-saas-blue tracking-wider mb-2">Most Profitable Month</p>
                    <h3 class="text-3xl font-bold text-saas-navy dark:text-white">${{ $bestMonth['month'] }}</h3>
                    <div class="mt-4 flex items-center text-[10px] text-saas-slate font-semibold uppercase tracking-widest">
                        Peak Performance
                    </div>
                </div>
            </div>

            {{-- Operational Shortcuts --}}
            <div class="flex flex-wrap gap-4 mb-10">
                @can('expenses.view')
                    <a href="{{ route('expenses.index') }}" class="inline-flex items-center px-6 py-3 bg-saas-danger text-white font-semibold rounded-saas hover:bg-red-700 transition group shadow-saas">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        Expenses Module
                    </a>
                @endcan

                <a href="{{ route('reports.profit') }}" class="inline-flex items-center px-6 py-3 bg-saas-blue text-white font-semibold rounded-saas hover:bg-blue-700 shadow-saas transition group">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Profit Report
                </a>

                <a href="{{ route('reports.cashflow') }}" class="inline-flex items-center px-6 py-3 bg-saas-soft text-saas-blue font-semibold rounded-saas border border-saas-soft hover:bg-blue-100 transition shadow-saas group">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Cash Flow Report
                </a>

                @can('products.view')
                    <a href="{{ route('products.index') }}" class="inline-flex items-center px-6 py-3 bg-white dark:bg-gray-800 text-saas-navy dark:text-gray-200 font-semibold rounded-saas border border-saas-border dark:border-gray-700 hover:bg-gray-50 shadow-saas transition group">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        Inventory
                    </a>
                @endcan

                <a href="{{ route('reports.master-o2c') }}" class="inline-flex items-center px-6 py-3 bg-white dark:bg-gray-800 text-saas-blue font-semibold rounded-saas border border-saas-soft hover:bg-blue-50 shadow-saas transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Full Reports
                </a>
            </div>

            {{-- Charts Section --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">
                <!-- Profit Trend (Line) -->
                <div class="bg-white dark:bg-gray-800 rounded-saas shadow-saas p-8 border border-saas-border dark:border-gray-700">
                    <h3 class="text-lg font-bold text-saas-navy dark:text-white mb-6 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-saas-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                        Profit Trend (Gross)
                    </h3>
                    <div class="relative h-72">
                        <canvas id="profitTrendChart"></canvas>
                    </div>
                </div>

                <!-- Expense Trend (Bar) -->
                <div class="bg-white dark:bg-gray-800 rounded-saas shadow-saas p-8 border border-saas-border dark:border-gray-700">
                    <h3 class="text-lg font-bold text-saas-navy dark:text-white mb-6 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-saas-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        Expense Outflow
                    </h3>
                    <div class="relative h-72">
                        <canvas id="expenseTrendChart"></canvas>
                    </div>
                </div>

                <!-- Stock Distribution (Doughnut) -->
                <div class="bg-white dark:bg-gray-800 rounded-saas shadow-saas p-8 border border-saas-border dark:border-gray-700">
                    <h3 class="text-lg font-bold text-saas-navy dark:text-white mb-6 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-saas-success" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path></svg>
                        Inventory Distribution
                    </h3>
                    <div class="relative h-64">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
                
                <!-- Transactions (Line) -->
                <div class="bg-white dark:bg-gray-800 rounded-saas shadow-saas p-8 border border-saas-border dark:border-gray-700">
                    <h3 class="text-lg font-bold text-saas-navy dark:text-white mb-6 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                        Activity Volume
                    </h3>
                    <div class="relative h-64">
                        <canvas id="transactionChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                {{-- Low Stock Alerts --}}
                <div class="bg-white dark:bg-gray-800 rounded-saas shadow-saas overflow-hidden border border-saas-border dark:border-gray-700">
                    <div class="p-6 border-b border-saas-border dark:border-gray-700 flex justify-between items-center bg-gray-50/30 dark:bg-gray-700/30">
                        <h3 class="text-lg font-bold text-saas-navy dark:text-white flex items-center">
                            <span class="w-2 h-2 bg-saas-danger rounded-full mr-2"></span>
                            Low Stock Alerts
                        </h3>
                        <span class="px-2 py-0.5 bg-red-50 text-saas-danger text-xs font-bold rounded-full border border-red-100">{{ $lowStockCount }} Items</span>
                    </div>
                    <div class="p-0">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-saas-slate uppercase bg-saas-bg dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-6 py-4 font-semibold">Product</th>
                                    <th class="px-6 py-4 font-semibold text-center">Stock</th>
                                    <th class="px-6 py-4 font-semibold text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-saas-border dark:divide-gray-700">
                                @forelse($lowStockProducts as $product)
                                    <tr class="hover:bg-saas-bg/50 dark:hover:bg-gray-700/30 transition">
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-saas-navy dark:text-white">{{ $product->name }}</div>
                                            <div class="text-xs text-saas-slate">{{ $product->category->name ?? 'Uncategorized' }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="font-bold text-saas-danger">{{ $product->stock }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            @can('transactions.create')
                                                <a href="{{ route('products.transactions.create', $product) }}" class="text-saas-blue hover:underline font-bold text-xs uppercase tracking-wider">Restock</a>
                                            @else
                                                <span class="text-saas-slate text-xs uppercase tracking-wider italic">View Only</span>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-10 text-center text-saas-success font-semibold bg-green-50/30 dark:bg-emerald-900/10">
                                            <svg class="w-8 h-8 mx-auto mb-2 text-saas-success" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            All products are well stocked!
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Recent Transactions --}}
                <div class="bg-white dark:bg-gray-800 rounded-saas shadow-saas overflow-hidden border border-saas-border dark:border-gray-700">
                    <div class="p-6 border-b border-saas-border dark:border-gray-700 flex justify-between items-center bg-gray-50/30 dark:bg-gray-700/30">
                        <h3 class="text-lg font-bold text-saas-navy dark:text-white">Recent Transactions</h3>
                        <a href="{{ route('transactions.index') }}" class="text-xs text-saas-blue hover:underline font-bold uppercase transition">View All</a>
                    </div>
                    <div class="p-0">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-saas-slate uppercase bg-saas-bg dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-6 py-4 font-semibold">Product</th>
                                    <th class="px-6 py-4 font-semibold text-center">Qty</th>
                                    <th class="px-6 py-4 font-semibold text-right">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-saas-border dark:divide-gray-700">
                                @forelse($recentTransactions as $transaction)
                                    <tr class="hover:bg-saas-bg/50 dark:hover:bg-gray-700/30 transition">
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-saas-navy dark:text-white">{{ $transaction->product->name }}</div>
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold border {{ $transaction->type === 'Masuk' ? 'bg-green-50 text-saas-success border-green-100' : 'bg-red-50 text-saas-danger border-red-100' }}">
                                                {{ strtoupper($transaction->type) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="font-bold {{ $transaction->type === 'Masuk' ? 'text-saas-success' : 'text-saas-danger' }}">
                                                {{ $transaction->type === 'Masuk' ? '+' : '-' }}{{ $transaction->quantity }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right text-xs text-saas-slate">
                                            {{ $transaction->created_at->diffForHumans() }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-10 text-center text-saas-slate font-medium italic">No transactions yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- Recent Activity Log --}}
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-saas shadow-saas overflow-hidden border border-saas-border dark:border-gray-700 mt-8">
                    <div class="p-6 border-b border-saas-border dark:border-gray-700 flex justify-between items-center bg-gray-50/30 dark:bg-gray-700/30">
                        <h3 class="text-lg font-bold text-saas-navy dark:text-white flex items-center">
                            <svg class="w-5 h-5 mr-2 text-saas-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Audit Trail / Recent Activity
                        </h3>
                    </div>
                    <div class="p-0">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-saas-slate uppercase bg-saas-bg dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-6 py-4 font-semibold">User</th>
                                    <th class="px-6 py-4 font-semibold">Action</th>
                                    <th class="px-6 py-4 font-semibold">Target</th>
                                    <th class="px-6 py-4 font-semibold text-right">Time</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-saas-border dark:divide-gray-700">
                                @forelse($recentActivities as $log)
                                    <tr class="hover:bg-saas-bg/50 dark:hover:bg-gray-700/30 transition">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="w-7 h-7 bg-saas-soft text-saas-blue rounded-full flex items-center justify-center font-bold text-xs mr-2">
                                                    {{ substr($log->user->name ?? 'S', 0, 1) }}
                                                </div>
                                                <span class="font-bold text-saas-navy dark:text-white">{{ $log->user->name ?? 'System' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold tracking-wider uppercase border
                                                {{ $log->action === 'created' ? 'bg-green-50 text-saas-success border-green-100' : '' }}
                                                {{ $log->action === 'updated' ? 'bg-blue-50 text-saas-blue border-blue-100' : '' }}
                                                {{ $log->action === 'deleted' ? 'bg-red-50 text-saas-danger border-red-100' : '' }}
                                            ">
                                                {{ $log->action }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-bold text-saas-navy dark:text-white">{{ class_basename($log->model_type) }} #{{ $log->model_id }}</div>
                                            @if($log->before_data || $log->after_data)
                                                <div class="text-[10px] text-saas-slate mt-1 space-y-0.5">
                                                    @if($log->action === 'updated')
                                                        @foreach(array_keys($log->after_data ?? []) as $field)
                                                            <div>
                                                                <span class="font-bold uppercase tracking-tight">{{ $field }}:</span> 
                                                                <span class="text-saas-danger line-through opacity-60">{{ is_array($log->before_data[$field] ?? '') ? 'json' : ($log->before_data[$field] ?? 'null') }}</span> → 
                                                                <span class="text-saas-success font-bold">{{ is_array($log->after_data[$field] ?? '') ? 'json' : ($log->after_data[$field] ?? 'null') }}</span>
                                                            </div>
                                                        @endforeach
                                                    @elseif($log->action === 'created')
                                                        <span class="text-saas-success font-bold uppercase tracking-wider text-[8px]">Snapshot Captured</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right text-xs text-saas-slate">
                                            {{ $log->created_at->diffForHumans() }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-10 text-center text-saas-slate italic">No activity recorded yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetch('{{ route('api.chart-data') }}')
                .then(response => response.json())
                .then(data => {
                    // Profit Trend Chart
                    const ctxProfit = document.getElementById('profitTrendChart').getContext('2d');
                    new Chart(ctxProfit, {
                        type: 'line',
                        data: {
                            labels: data.profitTrend.map(item => item.month),
                            datasets: [{
                                label: 'Gross Profit ($)',
                                data: data.profitTrend.map(item => item.total),
                                borderColor: '#2563EB',
                                backgroundColor: 'rgba(37, 99, 235, 0.05)',
                                fill: true,
                                tension: 0.3,
                                borderWidth: 3,
                                pointRadius: 0
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: { 
                                y: { 
                                    beginAtZero: true, 
                                    grid: { color: 'rgba(226, 232, 240, 0.5)' },
                                    ticks: { font: { size: 10, weight: 'semibold' }, color: '#475569' }
                                }, 
                                x: { 
                                    grid: { display: false },
                                    ticks: { font: { size: 10, weight: 'semibold' }, color: '#475569' }
                                } 
                            }
                        }
                    });

                    // Expense Trend Chart
                    const ctxExpense = document.getElementById('expenseTrendChart').getContext('2d');
                    new Chart(ctxExpense, {
                        type: 'bar',
                        data: {
                            labels: data.expenseTrend.map(item => item.month),
                            datasets: [{
                                label: 'Total Expenses ($)',
                                data: data.expenseTrend.map(item => item.total),
                                backgroundColor: '#DC2626',
                                borderRadius: 6,
                                barThickness: 20
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: { 
                                y: { 
                                    beginAtZero: true, 
                                    grid: { color: 'rgba(226, 232, 240, 0.5)' },
                                    ticks: { font: { size: 10, weight: 'semibold' }, color: '#475569' }
                                }, 
                                x: { 
                                    grid: { display: false },
                                    ticks: { font: { size: 10, weight: 'semibold' }, color: '#475569' }
                                } 
                            }
                        }
                    });

                    // Category Chart
                    const ctxCat = document.getElementById('categoryChart').getContext('2d');
                    new Chart(ctxCat, {
                        type: 'doughnut',
                        data: {
                            labels: data.stockByCategory.map(item => item.label),
                            datasets: [{
                                data: data.stockByCategory.map(item => item.value),
                                backgroundColor: ['#2563EB', '#16A34A', '#F59E0B', '#DC2626', '#8B5CF6'],
                                borderWidth: 4,
                                borderColor: '#ffffff'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { 
                                legend: { 
                                    position: 'bottom', 
                                    labels: { 
                                        boxWidth: 8, 
                                        padding: 15,
                                        font: { size: 11, weight: 'semibold' },
                                        color: '#475569'
                                    } 
                                } 
                            },
                            cutout: '80%'
                        }
                    });

                    // Transaction Chart
                    const ctxTrans = document.getElementById('transactionChart').getContext('2d');
                    new Chart(ctxTrans, {
                        type: 'line',
                        data: {
                            labels: data.monthlyTransactions.map(item => item.month),
                            datasets: [{
                                label: 'Volume',
                                data: data.monthlyTransactions.map(item => item.count),
                                borderColor: '#F59E0B',
                                tension: 0.3,
                                borderWidth: 3,
                                pointRadius: 0
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: { 
                                y: { display: false }, 
                                x: { 
                                    grid: { display: false },
                                    ticks: { font: { size: 10, weight: 'semibold' }, color: '#475569' }
                                } 
                            }
                        }
                    });
                });
        });
    </script>
    @endpush
</x-app-layout>
