<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Sales Orders') }}
            </h2>
            <div class="flex gap-3">
                <a href="{{ route('reports.sales-orders', ['format' => 'excel']) }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-emerald-600 dark:text-emerald-400 font-black rounded-xl hover:bg-emerald-50 dark:hover:bg-emerald-900/20 shadow-sm transition-all duration-200 text-[10px] uppercase tracking-[0.2em] group">
                    <svg class="w-4 h-4 mr-2 opacity-60 group-hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Excel
                </a>
                <a href="{{ route('reports.sales-orders', ['format' => 'pdf']) }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-rose-600 dark:text-rose-400 font-black rounded-xl hover:bg-rose-50 dark:hover:bg-rose-900/20 shadow-sm transition-all duration-200 text-[10px] uppercase tracking-[0.2em] group">
                    <svg class="w-4 h-4 mr-2 opacity-60 group-hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    PDF
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('partials.status-messages')

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-saas sm:rounded-2xl border border-gray-100 dark:border-gray-700">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left border-collapse">
                        <thead class="text-[10px] text-gray-400 uppercase bg-gray-50/50 dark:bg-gray-900/50 font-black tracking-widest border-b border-gray-100 dark:border-gray-700">
                            <tr>
                                <th class="px-6 py-5">SO Number</th>
                                <th class="px-6 py-5">Customer</th>
                                <th class="px-6 py-5 text-center">Status</th>
                                <th class="px-6 py-5">From RO</th>
                                <th class="px-6 py-5 text-right">Total</th>
                                <th class="px-6 py-5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                            @foreach($salesOrders as $so)
                                <tr class="group hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-all duration-200">
                                    <td class="px-6 py-4 font-black text-gray-900 dark:text-white">
                                        {{ $so->sales_number }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ $so->customer_name }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @php
                                            $statusColors = [
                                                'draft' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                                'confirmed' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                                                'delivered' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400',
                                                'invoiced' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400',
                                                'cancelled' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-400',
                                            ];
                                        @endphp
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider {{ $statusColors[$so->status] }}">
                                            {{ $so->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($so->request_order_id)
                                            <span class="text-[11px] font-bold text-gray-500 uppercase tracking-tighter">
                                                {{ $so->requestOrder->request_number }}
                                            </span>
                                        @else
                                            <span class="text-gray-300 dark:text-gray-600 font-bold">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="font-black text-gray-900 dark:text-white">{{ $so->formatted_amount }}</div>
                                        @if($so->currency && !$so->currency->is_base)
                                            <div class="text-[10px] text-gray-400 uppercase font-black tracking-tighter opacity-60">≈ {{ $so->formatted_base_amount }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('sales-orders.show', $so) }}" class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-black rounded-xl hover:bg-indigo-600 hover:text-white transition-all duration-200 text-[10px] uppercase tracking-widest">
                                            Manage
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
