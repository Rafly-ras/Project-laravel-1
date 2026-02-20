<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Inventory Dashboard') }}
            </h2>
            <a href="{{ route('reports.master-o2c') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white font-black rounded-xl hover:bg-indigo-700 shadow-md transition transform hover:-translate-y-0.5 text-xs uppercase tracking-[0.2em]">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Master O2C Report
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Warehouse Filter --}}
            <div class="mb-8 flex justify-end">
                <form action="{{ route('dashboard') }}" method="GET" class="flex items-center space-x-2">
                    <label for="warehouse_id" class="text-xs font-bold text-gray-500 uppercase tracking-widest">Filter by Warehouse:</label>
                    <select name="warehouse_id" id="warehouse_id" onchange="this.form.submit()" class="text-sm rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 shadow-sm focus:ring-indigo-500 py-2 pr-10">
                        <option value="">All Warehouses</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" {{ $warehouseId == $warehouse->id ? 'selected' : '' }}>
                                {{ $warehouse->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
            <!-- O2C Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                <div class="bg-indigo-600 rounded-3xl p-8 shadow-xl shadow-indigo-200 dark:shadow-none text-white overflow-hidden relative group">
                    <div class="relative z-10">
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-80 mb-2">Total Revenue</p>
                        <h3 class="text-4xl font-black">${{ number_format($totalRevenue, 2) }}</h3>
                        <div class="mt-4 flex items-center text-xs font-bold bg-white/10 w-fit px-3 py-1 rounded-full">
                            Lifespan Sales
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-3xl p-8 shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden relative group">
                    <div class="relative z-10">
                        <p class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] mb-2">Pending Requests</p>
                        <h3 class="text-4xl font-black text-gray-900 dark:text-white">{{ $pendingROs }}</h3>
                        <a href="{{ route('request-orders.index') }}" class="mt-4 inline-block text-xs font-bold text-indigo-600 hover:underline">View RO Queue →</a>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-3xl p-8 shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden relative group">
                    <div class="relative z-10">
                        <p class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] mb-2">Outstanding Balance</p>
                        <h3 class="text-4xl font-black text-rose-500">${{ number_format($unpaidInvoiceTotal, 2) }}</h3>
                        <div class="mt-4 flex items-center text-xs font-black text-rose-500 uppercase tracking-widest bg-rose-50 dark:bg-rose-900/20 w-fit px-3 py-1 rounded-full">
                            Unpaid Invoices
                        </div>
                    </div>
                </div>
            </div>

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
                @can('products.view')
                    <a href="{{ route('products.index') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 shadow-sm hover:shadow-md transition group">
                        <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        Manage Products
                    </a>
                @endcan

                @can('transactions.view')
                    <a href="{{ route('transactions.index') }}" class="inline-flex items-center px-6 py-3 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 font-bold rounded-xl border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 shadow-sm hover:shadow-md transition group">
                        <svg class="w-5 h-5 mr-2 group-hover:rotate-12 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        Transactions
                    </a>
                @endcan

                @can('reports.view')
                    <a href="{{ route('products.stock-summary') }}" class="inline-flex items-center px-6 py-3 bg-white dark:bg-gray-800 text-emerald-600 dark:text-emerald-400 font-bold rounded-xl border border-emerald-100 dark:border-emerald-900/30 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 shadow-sm hover:shadow-md transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Stock Summary
                    </a>
                @endcan

                @can('transactions.create')
                    <a href="{{ route('transactions.create') }}" class="inline-flex items-center px-6 py-3 bg-white dark:bg-gray-800 text-indigo-600 dark:text-indigo-400 font-bold rounded-xl border border-indigo-100 dark:border-indigo-900/30 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 shadow-sm hover:shadow-md transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        Record Movement
                    </a>
                @endcan
            </div>

            {{-- Charts Section --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">
                <!-- Stock by Category (Pie/Doughnut) -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6 border border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-black text-gray-900 dark:text-white mb-6 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
                        Stock Distribution
                    </h3>
                    <div class="relative h-64">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>

                <!-- Monthly Transactions (Line/Bar) -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6 border border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-black text-gray-900 dark:text-white mb-6 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                        Transaction Trends
                    </h3>
                    <div class="relative h-64">
                        <canvas id="transactionChart"></canvas>
                    </div>
                </div>
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
                                            @can('transactions.create')
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
                {{-- Recent Activity Log --}}
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden border border-gray-100 dark:border-gray-700 mt-8">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-700/30">
                        <h3 class="text-lg font-black text-gray-900 dark:text-white flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Audit Trail / Recent Activity
                        </h3>
                    </div>
                    <div class="p-0">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-6 py-4">User</th>
                                    <th class="px-6 py-4">Action</th>
                                    <th class="px-6 py-4">Target</th>
                                    <th class="px-6 py-4 text-right">Time</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse($recentActivities as $log)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="w-7 h-7 bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 rounded-full flex items-center justify-center font-bold text-xs mr-2">
                                                    {{ substr($log->user->name ?? 'S', 0, 1) }}
                                                </div>
                                                <span class="font-medium text-gray-900 dark:text-white">{{ $log->user->name ?? 'System' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black tracking-wider uppercase
                                                {{ $log->action === 'created' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400' : '' }}
                                                {{ $log->action === 'updated' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400' : '' }}
                                                {{ $log->action === 'deleted' ? 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-400' : '' }}
                                            ">
                                                {{ $log->action }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-gray-500">
                                            {{ class_basename($log->model_type) }} #{{ $log->model_id }}
                                        </td>
                                        <td class="px-6 py-4 text-right text-xs text-gray-500">
                                            {{ $log->created_at->diffForHumans() }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-10 text-center text-gray-400 italic">No activity recorded yet.</td>
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
                    // Category Chart
                    const ctxCat = document.getElementById('categoryChart').getContext('2d');
                    new Chart(ctxCat, {
                        type: 'doughnut',
                        data: {
                            labels: data.stockByCategory.map(item => item.label),
                            datasets: [{
                                data: data.stockByCategory.map(item => item.value),
                                backgroundColor: [
                                    '#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'
                                ],
                                borderWidth: 0,
                                hoverOffset: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { position: 'bottom' }
                            },
                            cutout: '70%'
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
                                borderColor: '#4f46e5',
                                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                                fill: true,
                                tension: 0.4,
                                borderWidth: 3,
                                pointRadius: 4,
                                pointBackgroundColor: '#4f46e5'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: { beginAtZero: true, grid: { display: false } },
                                x: { grid: { display: false } }
                            },
                            plugins: {
                                legend: { display: false }
                            }
                        }
                    });
                });
        });
    </script>
    @endpush
</x-app-layout>
