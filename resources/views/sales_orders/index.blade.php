<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Sales Orders') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('reports.sales-orders', ['format' => 'excel']) }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white font-bold rounded-xl hover:bg-emerald-700 shadow-sm transition text-xs uppercase tracking-widest">Excel</a>
                <a href="{{ route('reports.sales-orders', ['format' => 'pdf']) }}" class="inline-flex items-center px-4 py-2 bg-rose-600 text-white font-bold rounded-xl hover:bg-rose-700 shadow-sm transition text-xs uppercase tracking-widest">PDF</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('partials.status-messages')

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 dark:border-gray-700">
                <div class="p-0">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-6 py-4">SO Number</th>
                                <th class="px-6 py-4">Customer</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4">From RO</th>
                                <th class="px-6 py-4">Total</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($salesOrders as $so)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                                    <td class="px-6 py-4 font-black text-gray-900 dark:text-white">
                                        {{ $so->sales_number }}
                                    </td>
                                    <td class="px-6 py-4 font-bold">
                                        {{ $so->customer_name }}
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
                                    <td class="px-6 py-4 text-xs font-bold text-gray-500">
                                        {{ $so->requestOrder->request_number ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 font-black">
                                         ${{ number_format($so->total_amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('sales-orders.show', $so) }}" class="text-indigo-600 hover:text-indigo-900 font-bold text-xs uppercase tracking-wider">Manage</a>
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
