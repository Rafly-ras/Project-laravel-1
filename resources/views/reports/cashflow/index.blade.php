<x-app-layout>
    <div class="py-12 bg-gray-50/50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Header & Filters --}}
            <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">Financial Cash Flow</h2>
                    <p class="text-gray-500 dark:text-gray-400 mt-1 font-medium">Real-time liquidity analysis based on confirmed payments and expenses.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('reports.cashflow.csv', request()->all()) }}" class="px-6 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 font-bold rounded-xl text-xs uppercase tracking-widest shadow-sm hover:bg-gray-50 transition flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        CSV
                    </a>
                    <a href="{{ route('reports.cashflow.pdf', request()->all()) }}" class="px-6 py-2.5 bg-indigo-600 text-white font-black rounded-xl text-xs uppercase tracking-widest shadow-lg shadow-indigo-200 dark:shadow-none hover:bg-indigo-700 transition flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Export PDF
                    </a>
                </div>
            </div>

            {{-- Filter Bar --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mb-8">
                <form action="{{ route('reports.cashflow') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
                    <div>
                        <label class="block text-[10px] font-black uppercase text-gray-400 tracking-[0.2em] mb-2">From Date</label>
                        <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="w-full rounded-xl border-gray-100 dark:bg-gray-700 dark:border-gray-600 text-sm">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase text-gray-400 tracking-[0.2em] mb-2">To Date</label>
                        <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="w-full rounded-xl border-gray-100 dark:bg-gray-700 dark:border-gray-600 text-sm">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 px-6 py-2.5 bg-gray-900 dark:bg-indigo-600 text-white font-black rounded-xl text-xs uppercase tracking-widest hover:opacity-90 transition">
                            Apply Filter
                        </button>
                        <a href="{{ route('reports.cashflow') }}" class="px-6 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 font-bold rounded-xl text-xs uppercase tracking-widest hover:bg-gray-200 transition">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            {{-- KPI Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                <div class="bg-white dark:bg-gray-800 p-8 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-500/10 rounded-full -mr-16 -mt-16 group-hover:scale-110 transition duration-500"></div>
                    <p class="text-xs font-black text-emerald-500 uppercase tracking-widest mb-2 flex items-center">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2"></span> Cash Inflow
                    </p>
                    <h3 class="text-4xl font-black text-gray-900 dark:text-white">${{ number_format($summary['cash_in'], 2) }}</h3>
                    <p class="text-gray-400 text-xs mt-3 font-medium">Total payments captured in period</p>
                </div>

                <div class="bg-white dark:bg-gray-800 p-8 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-rose-500/10 rounded-full -mr-16 -mt-16 group-hover:scale-110 transition duration-500"></div>
                    <p class="text-xs font-black text-rose-500 uppercase tracking-widest mb-2 flex items-center">
                        <span class="w-2 h-2 bg-rose-500 rounded-full mr-2"></span> Cash Outflow
                    </p>
                    <h3 class="text-4xl font-black text-gray-900 dark:text-white">${{ number_format($summary['cash_out'], 2) }}</h3>
                    <p class="text-gray-400 text-xs mt-3 font-medium">Total expenses recorded in period</p>
                </div>

                <div class="bg-white dark:bg-gray-800 p-8 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-32 h-32 {{ $summary['net_cashflow'] >= 0 ? 'bg-indigo-500/10' : 'bg-red-500/10' }} rounded-full -mr-16 -mt-16 group-hover:scale-110 transition duration-500"></div>
                    <p class="text-xs font-black {{ $summary['net_cashflow'] >= 0 ? 'text-indigo-500' : 'text-red-500' }} uppercase tracking-widest mb-2 flex items-center">
                        <span class="w-2 h-2 {{ $summary['net_cashflow'] >= 0 ? 'bg-indigo-500' : 'bg-red-500' }} rounded-full mr-2"></span> Net Liquidity
                    </p>
                    <h3 class="text-4xl font-black text-gray-900 dark:text-white">${{ number_format($summary['net_cashflow'], 2) }}</h3>
                    <p class="text-gray-400 text-xs mt-3 font-medium">Overall surplus/deficit for period</p>
                </div>
            </div>

            {{-- Chart Section --}}
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-8 mb-8">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-xl font-black text-gray-900 dark:text-white tracking-tight flex items-center">
                        <svg class="w-6 h-6 mr-3 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                        Liquidity Trend (12 Months)
                    </h3>
                </div>
                <div class="h-80 relative">
                    <canvas id="cashflowTrendChart"></canvas>
                </div>
            </div>

            {{-- Detailed Tables --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                {{-- Cash In Table --}}
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700 bg-emerald-50/30 dark:bg-emerald-900/10 flex justify-between items-center">
                        <h3 class="text-sm font-black text-emerald-700 dark:text-emerald-400 uppercase tracking-widest flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path></svg>
                            Cash Received
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-[10px] text-gray-400 uppercase tracking-widest bg-gray-50/50 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-6 py-4">Date</th>
                                    <th class="px-6 py-4">Ref/INV</th>
                                    <th class="px-6 py-4">Customer</th>
                                    <th class="px-6 py-4 text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse($cashIn as $pay)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                                        <td class="px-6 py-4 font-medium text-gray-500">{{ $pay->paid_at->format('M d, Y') }}</td>
                                        <td class="px-6 py-4">
                                            <div class="font-black text-gray-900 dark:text-white text-xs">{{ $pay->payment_number }}</div>
                                            <div class="text-[10px] text-gray-400">{{ $pay->invoice->invoice_number }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400 text-xs">{{ $pay->invoice->salesOrder->customer_name }}</td>
                                        <td class="px-6 py-4 text-right font-black text-emerald-600">${{ number_format($pay->amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="px-6 py-10 text-center text-gray-400 italic">No incoming cash found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-6 border-t border-gray-100 dark:border-gray-700">
                        {{ $cashIn->links() }}
                    </div>
                </div>

                {{-- Cash Out Table --}}
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700 bg-rose-50/30 dark:bg-rose-900/10 flex justify-between items-center">
                        <h3 class="text-sm font-black text-rose-700 dark:text-rose-400 uppercase tracking-widest flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"></path></svg>
                            Cash Spent
                        </h3>
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
                                @forelse($cashOut as $exp)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                                        <td class="px-6 py-4 font-medium text-gray-500">{{ $exp->expense_date->format('M d, Y') }}</td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-0.5 bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400 rounded text-[10px] font-black uppercase tracking-wider">
                                                {{ $exp->category->name }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400 text-xs truncate max-w-[150px]">{{ $exp->description }}</td>
                                        <td class="px-6 py-4 text-right font-black text-rose-600">${{ number_format($exp->amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="px-6 py-10 text-center text-gray-400 italic">No outgoing cash found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-6 border-t border-gray-100 dark:border-gray-700">
                        {{ $cashOut->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('cashflowTrendChart').getContext('2d');
            const data = @json($monthlyBreakdown);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.map(item => item.month),
                    datasets: [
                        {
                            label: 'Cash In',
                            data: data.map(item => item.cash_in),
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            fill: true,
                            tension: 0.4,
                            borderWidth: 3,
                            pointRadius: 4,
                            pointBackgroundColor: '#10b981'
                        },
                        {
                            label: 'Cash Out',
                            data: data.map(item => item.cash_out),
                            borderColor: '#ef4444',
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            fill: true,
                            tension: 0.4,
                            borderWidth: 3,
                            pointRadius: 4,
                            pointBackgroundColor: '#ef4444'
                        },
                        {
                            label: 'Net Cashflow',
                            data: data.map(item => item.net),
                            type: 'bar',
                            backgroundColor: 'rgba(79, 70, 229, 0.8)',
                            borderRadius: 6,
                            barThickness: 20
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 12, font: { weight: 'bold' } } }
                    },
                    scales: {
                        y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                        x: { grid: { display: false } }
                    }
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
