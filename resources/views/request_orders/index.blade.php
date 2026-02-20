<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Request Orders') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('reports.request-orders', ['format' => 'excel']) }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white font-bold rounded-xl hover:bg-emerald-700 shadow-sm transition text-xs uppercase tracking-widest">Excel</a>
                <a href="{{ route('reports.request-orders', ['format' => 'pdf']) }}" class="inline-flex items-center px-4 py-2 bg-rose-600 text-white font-bold rounded-xl hover:bg-rose-700 shadow-sm transition text-xs uppercase tracking-widest">PDF</a>
                <a href="{{ route('request-orders.create') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-black rounded-xl hover:bg-indigo-700 shadow-sm hover:shadow-md transition transform hover:-translate-y-0.5">
                    + New Request
                </a>
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
                                <th class="px-6 py-4">RO Number</th>
                                <th class="px-6 py-4">Customer</th>
                                <th class="px-6 py-4">Amount</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4">Created By</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($requestOrders as $ro)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                                    <td class="px-6 py-4 font-black text-gray-900 dark:text-white">
                                        {{ $ro->request_number }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900 dark:text-white">{{ $ro->customer_name }}</div>
                                        <div class="text-xs text-gray-500">{{ $ro->customer_email }}</div>
                                    </td>
                                    <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">
                                        ${{ number_format($ro->total_amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @php
                                            $statusColors = [
                                                'draft' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                                'approved' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400',
                                                'rejected' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-400',
                                                'converted' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400',
                                            ];
                                        @endphp
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider {{ $statusColors[$ro->status] }}">
                                            {{ $ro->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-xs text-gray-500 font-bold uppercase">
                                        {{ $ro->creator->name }}
                                        <div class="text-[10px] font-normal leading-tight">{{ $ro->created_at->format('M d, Y') }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('request-orders.show', $ro) }}" class="text-indigo-600 hover:text-indigo-900 font-bold text-xs uppercase tracking-wider">Details</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-6 border-t border-gray-100 dark:border-gray-700">
                    {{ $requestOrders->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
