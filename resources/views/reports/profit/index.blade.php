<x-app-layout>
    <div class="py-12 bg-gray-50/50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Header & Filters --}}
            <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">Profit & Loss Statement</h2>
                    <p class="text-gray-500 dark:text-gray-400 mt-1 font-medium italic">Accrual Basis Reporting (Revenue recognized on sale confirmation)</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('reports.profit.csv', request()->all()) }}" class="px-6 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 font-bold rounded-xl text-xs uppercase tracking-widest shadow-sm hover:bg-gray-50 transition flex items-center">
                         CSV
                    </a>
                    <a href="{{ route('reports.profit.pdf', request()->all()) }}" class="px-6 py-2.5 bg-indigo-600 text-white font-black rounded-xl text-xs uppercase tracking-widest shadow-lg shadow-indigo-200 dark:shadow-none hover:bg-indigo-700 transition flex items-center">
                        Export PDF
                    </a>
                </div>
            </div>

            {{-- Filter Bar --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mb-8">
                <form action="{{ route('reports.profit') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
                    <div>
                        <label class="block text-[10px] font-black uppercase text-gray-400 tracking-[0.2em] mb-2">Start Date</label>
                        <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="w-full rounded-xl border-gray-100 dark:bg-gray-700 dark:border-gray-600 text-sm">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase text-gray-400 tracking-[0.2em] mb-2">End Date</label>
                        <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="w-full rounded-xl border-gray-100 dark:bg-gray-700 dark:border-gray-600 text-sm">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 px-6 py-2.5 bg-gray-900 dark:bg-indigo-600 text-white font-black rounded-xl text-xs uppercase tracking-widest hover:opacity-90 transition">
                            Update
                        </button>
                        <a href="{{ route('reports.profit') }}" class="px-6 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 font-bold rounded-xl text-xs uppercase tracking-widest hover:bg-gray-200 transition">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            {{-- KPI Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-8">
                <div class="bg-white dark:bg-gray-800 p-8 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 flex items-center">
                        Total Revenue
                    </p>
                    <h3 class="text-3xl font-black text-gray-900 dark:text-white">${{ number_format($summary['revenue'], 2) }}</h3>
                    <div class="mt-4 flex items-center justify-between">
                        <span class="text-[10px] font-bold text-gray-400 tracking-wider">Gross Profit: ${{ number_format($summary['gross_profit'], 2) }}</span>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 p-8 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 flex items-center">
                        Operating Expenses
                    </p>
                    <h3 class="text-3xl font-black text-rose-600">${{ number_format($summary['expenses'], 2) }}</h3>
                    <div class="mt-4 flex items-center justify-between">
                        <span class="text-[10px] font-bold text-gray-400 tracking-wider">COGS: ${{ number_format($summary['cogs'], 2) }}</span>
                    </div>
                </div>

                <div class="bg-indigo-600 p-8 rounded-3xl shadow-xl shadow-indigo-100 dark:shadow-none text-white relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16 group-hover:scale-110 transition duration-500"></div>
                    <p class="text-[10px] font-black text-white/70 uppercase tracking-widest mb-2">Net Profit (EBITDA)</p>
                    <h3 class="text-3xl font-black">${{ number_format($summary['net_profit'], 2) }}</h3>
                    <div class="mt-4 inline-flex items-center text-[10px] font-black bg-white/20 px-3 py-1 rounded-full uppercase tracking-widest">
                        {{ number_format($summary['margin_percentage'], 1) }}% Net Margin
                    </div>
                </div>
            </div>

            {{-- Charts --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-8">
                    <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest mb-6 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                        Revenue vs Expense Trend
                    </h3>
                    <div class="h-64 relative">
                        <canvas id="revenueTrendChart"></canvas>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-8">
                    <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest mb-6 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        Monthly Net Profit
                    </h3>
                    <div class="h-64 relative">
                        <canvas id="netProfitChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Tables --}}
            <div class="grid grid-cols-1 gap-8">
                {{-- Revenue Breakdown --}}
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                        <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest">Revenue Breakdown</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-[10px] text-gray-400 uppercase tracking-widest bg-gray-50/50 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-6 py-4">Sales Order</th>
                                    <th class="px-6 py-4">Confirmed Date</th>
                                    <th class="px-6 py-4">Customer</th>
                                    <th class="px-6 py-4 text-right">Revenue</th>
                                    <th class="px-6 py-4 text-right">COGS</th>
                                    <th class="px-6 py-4 text-right">Gross Profit</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse($revenueDetails as $so)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                                        <td class="px-6 py-4 font-black text-indigo-600">{{ $so->sales_number }}</td>
                                        <td class="px-6 py-4 text-gray-500">{{ $so->confirmed_at->format('M d, Y') }}</td>
                                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400 font-medium">{{ $so->customer_name }}</td>
                                        <td class="px-6 py-4 text-right font-black text-gray-900 dark:text-white">${{ number_format($so->total_amount, 2) }}</td>
                                        <td class="px-6 py-4 text-right text-rose-500 font-bold">${{ number_format($so->total_amount - $so->gross_profit, 2) }}</td>
                                        <td class="px-6 py-4 text-right font-black text-emerald-600">${{ number_format($so->gross_profit, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="px-6 py-10 text-center text-gray-400 italic">No revenue data for this period.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-6 border-t border-gray-100 dark:border-gray-700">
                        {{ $revenueDetails->links() }}
                    </div>
                </div>

                {{-- Expense Breakdown --}}
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/30">
                        <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest">Expense Breakdown</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-[10px] text-gray-400 uppercase tracking-widest bg-gray-50/50 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-6 py-4">Date</th>
                                    <th class="px-6 py-4">Category</th>
                                    <th class="px-6 py-4">Description</th>
                                    <th class="px-6 py-4 text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse($expenseDetails as $exp)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                                        <td class="px-6 py-4 text-gray-500 font-medium">{{ $exp->expense_date->format('M d, Y') }}</td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded text-[10px] font-black uppercase tracking-wider">
                                                {{ $exp->category->name }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400 text-xs">{{ $exp->description }}</td>
                                        <td class="px-6 py-4 text-right font-black text-rose-600">${{ number_format($exp->amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="px-6 py-10 text-center text-gray-400 italic">No expense data for this period.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-6 border-t border-gray-100 dark:border-gray-700">
                        {{ $expenseDetails->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const trendData = @json($monthlyBreakdown);
            
            // Revenue vs Expense Line Chart
            new Chart(document.getElementById('revenueTrendChart').getContext('2d'), {
                type: 'line',
                data: {
                    labels: trendData.map(d => d.month),
                    datasets: [
                        {
                            label: 'Revenue',
                            data: trendData.map(d => d.revenue),
                            borderColor: '#4f46e5',
                            backgroundColor: 'rgba(79, 70, 229, 0.1)',
                            fill: true, tension: 0.4, borderWidth: 3
                        },
                        {
                            label: 'Expenses',
                            data: trendData.map(d => d.expense),
                            borderColor: '#f43f5e',
                            backgroundColor: 'rgba(244, 63, 94, 0.1)',
                            fill: true, tension: 0.4, borderWidth: 3
                        }
                    ]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { weight: 'bold' } } } },
                    scales: { y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.03)' } }, x: { grid: { display: false } } }
                }
            });

            // Net Profit Bar Chart
            new Chart(document.getElementById('netProfitChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: trendData.map(d => d.month),
                    datasets: [{
                        label: 'Net Profit',
                        data: trendData.map(d => d.net_profit),
                        backgroundColor: trendData.map(d => d.net_profit >= 0 ? 'rgba(16, 185, 129, 0.8)' : 'rgba(244, 63, 94, 0.8)'),
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { grid: { color: 'rgba(0,0,0,0.03)' } }, x: { grid: { display: false } } }
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
