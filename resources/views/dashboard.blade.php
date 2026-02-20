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
            <!-- Financial Intel Engine -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
                {{-- Total Revenue --}}
                <div class="bg-indigo-600 rounded-3xl p-6 shadow-xl shadow-indigo-200 dark:shadow-none text-white overflow-hidden relative group">
                    <div class="relative z-10">
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-80 mb-2">Total Revenue</p>
                        <h3 class="text-3xl font-black">${{ number_format($totalRevenue, 2) }}</h3>
                        <div class="mt-4 flex items-center text-[10px] font-bold bg-white/10 w-fit px-3 py-1 rounded-full uppercase tracking-widest">
                            Confirmed Sales
                        </div>
                    </div>
                </div>

                {{-- Gross Profit --}}
                <div class="bg-emerald-600 rounded-3xl p-6 shadow-xl shadow-emerald-200 dark:shadow-none text-white overflow-hidden relative group">
                    <div class="relative z-10">
                        <div class="flex justify-between items-start">
                            <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-80 mb-2">Gross Profit</p>
                            <span class="text-[10px] font-black bg-white/20 px-2 py-0.5 rounded-lg border border-white/30">{{ number_format($marginPercentage, 1) }}% Margin</span>
                        </div>
                        <h3 class="text-3xl font-black">${{ number_format($totalGrossProfit, 2) }}</h3>
                        <div class="mt-4 flex items-center text-[10px] font-bold bg-white/10 w-fit px-3 py-1 rounded-full uppercase tracking-widest">
                            Revenue - COGS
                        </div>
                    </div>
                </div>

                {{-- Total Expenses --}}
                <div class="bg-rose-600 rounded-3xl p-6 shadow-xl shadow-rose-200 dark:shadow-none text-white overflow-hidden relative group">
                    <div class="relative z-10">
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-80 mb-2">Total Expenses</p>
                        <h3 class="text-3xl font-black">${{ number_format($totalExpenses, 2) }}</h3>
                        <a href="{{ route('expenses.index') }}" class="mt-4 flex items-center text-[10px] font-bold bg-white/10 hover:bg-white/20 transition w-fit px-3 py-1 rounded-full uppercase tracking-widest">
                            Manage Expenses →
                        </a>
                    </div>
                </div>

                {{-- Net Profit / Cashflow --}}
                <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden relative group">
                    <div class="relative z-10">
                        <p class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] mb-2">Net Cashflow</p>
                        <h3 class="text-3xl font-black {{ $netProfit >= 0 ? 'text-indigo-600' : 'text-rose-600' }}">
                            ${{ number_format($netProfit, 2) }}
                        </h3>
                        <div class="mt-4 flex items-center text-[10px] font-black {{ $netProfit >= 0 ? 'text-indigo-500 bg-indigo-50' : 'text-rose-500 bg-rose-50' }} dark:bg-gray-700 uppercase tracking-widest w-fit px-3 py-1 rounded-full">
                            {{ $netProfit >= 0 ? 'Surplus' : 'Deficit' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Operational Shortcuts --}}
            <div class="flex flex-wrap gap-4 mb-10">
                @can('expenses.view')
                    <a href="{{ route('expenses.index') }}" class="inline-flex items-center px-6 py-3 bg-rose-600 text-white font-bold rounded-xl hover:bg-rose-700 shadow-sm transition group">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        Expenses Module
                    </a>
                @endcan

                @can('products.view')
                    <a href="{{ route('products.index') }}" class="inline-flex items-center px-6 py-3 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 font-bold rounded-xl border border-gray-200 dark:border-gray-700 hover:bg-gray-50 shadow-sm transition group">
                        <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        Inventory
                    </a>
                @endcan

                <a href="{{ route('reports.master-o2c') }}" class="inline-flex items-center px-6 py-3 bg-white dark:bg-gray-800 text-indigo-600 font-bold rounded-xl border border-indigo-100 hover:bg-indigo-50 shadow-sm transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Full Reports
                </a>
            </div>

            {{-- Charts Section --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">
                <!-- Profit Trend (Line) -->
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm p-8 border border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-black text-gray-900 dark:text-white mb-6 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                        Profit Trend (Gross)
                    </h3>
                    <div class="relative h-72">
                        <canvas id="profitTrendChart"></canvas>
                    </div>
                </div>

                <!-- Expense Trend (Bar) -->
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm p-8 border border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-black text-gray-900 dark:text-white mb-6 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        Expense Outflow
                    </h3>
                    <div class="relative h-72">
                        <canvas id="expenseTrendChart"></canvas>
                    </div>
                </div>

                <!-- Stock Distribution (Doughnut) -->
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm p-8 border border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-black text-gray-900 dark:text-white mb-6 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path></svg>
                        Inventory Distribution
                    </h3>
                    <div class="relative h-64">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
                
                <!-- Transactions (Line) -->
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm p-8 border border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-black text-gray-900 dark:text-white mb-6 flex items-center">
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
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-bold text-gray-900 dark:text-white">{{ class_basename($log->model_type) }} #{{ $log->model_id }}</div>
                                            @if($log->before_data || $log->after_data)
                                                <div class="text-[10px] text-gray-400 mt-1 space-y-0.5">
                                                    @if($log->action === 'updated')
                                                        @foreach(array_keys($log->after_data ?? []) as $field)
                                                            <div>
                                                                <span class="font-black uppercase tracking-tighter">{{ $field }}:</span> 
                                                                <span class="text-rose-400 line-through decoration-1">{{ is_array($log->before_data[$field] ?? '') ? 'json' : ($log->before_data[$field] ?? 'null') }}</span> → 
                                                                <span class="text-emerald-500">{{ is_array($log->after_data[$field] ?? '') ? 'json' : ($log->after_data[$field] ?? 'null') }}</span>
                                                            </div>
                                                        @endforeach
                                                    @elseif($log->action === 'created')
                                                        <span class="text-emerald-500 font-bold uppercase tracking-widest text-[8px]">Full Snapshot Captured</span>
                                                    @endif
                                                </div>
                                            @endif
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
                    // Profit Trend Chart
                    const ctxProfit = document.getElementById('profitTrendChart').getContext('2d');
                    new Chart(ctxProfit, {
                        type: 'line',
                        data: {
                            labels: data.profitTrend.map(item => item.month),
                            datasets: [{
                                label: 'Gross Profit ($)',
                                data: data.profitTrend.map(item => item.total),
                                borderColor: '#4f46e5',
                                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                                fill: true,
                                tension: 0.4,
                                borderWidth: 3
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: { y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } }, x: { grid: { display: false } } }
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
                                backgroundColor: '#ef4444',
                                borderRadius: 8
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: { y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } }, x: { grid: { display: false } } }
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
                                backgroundColor: ['#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'],
                                borderWidth: 0
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, padding: 20 } } },
                            cutout: '75%'
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
                                borderColor: '#f59e0b',
                                tension: 0.4,
                                borderWidth: 3,
                                pointRadius: 0
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: { y: { display: false }, x: { grid: { display: false } } }
                        }
                    });
                });
        });
    </script>
    @endpush
</x-app-layout>
