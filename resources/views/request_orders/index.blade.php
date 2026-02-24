<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Request Orders') }}
            </h2>
            <div class="flex gap-3 items-center">
                <a href="{{ route('reports.request-orders', ['format' => 'excel']) }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-emerald-600 dark:text-emerald-400 font-black rounded-xl hover:bg-emerald-50 dark:hover:bg-emerald-900/20 shadow-sm transition-all duration-200 text-[10px] uppercase tracking-[0.2em] group">
                    <svg class="w-4 h-4 mr-2 opacity-60 group-hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Excel
                </a>
                <a href="{{ route('reports.request-orders', ['format' => 'pdf']) }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-rose-600 dark:text-rose-400 font-black rounded-xl hover:bg-rose-50 dark:hover:bg-rose-900/20 shadow-sm transition-all duration-200 text-[10px] uppercase tracking-[0.2em] group">
                    <svg class="w-4 h-4 mr-2 opacity-60 group-hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    PDF
                </a>
                <a href="{{ route('request-orders.create') }}" class="inline-flex items-center px-6 py-2.5 bg-indigo-600 text-white font-black rounded-xl hover:bg-indigo-700 shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5 text-[10px] uppercase tracking-[0.2em] ml-2">
                    <span class="mr-2 text-base">+</span> New Request
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
                                <th class="px-6 py-5">RO Number</th>
                                <th class="px-6 py-5">Customer</th>
                                <th class="px-6 py-5 text-right">Amount</th>
                                <th class="px-6 py-5 text-center">Status</th>
                                <th class="px-6 py-5 text-right">Created By</th>
                                <th class="px-6 py-5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                            @foreach($requestOrders as $ro)
                                <tr class="group hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-all duration-200">
                                    <td class="px-6 py-4 font-black text-gray-900 dark:text-white">
                                        {{ $ro->request_number }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ $ro->customer_name }}</div>
                                        <div class="text-[10px] text-gray-500 font-bold uppercase">{{ $ro->customer_email }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="font-black text-gray-900 dark:text-white">{{ $ro->formatted_amount }}</div>
                                        @if($ro->currency && !$ro->currency->is_base)
                                            <div class="text-[10px] text-gray-400 uppercase font-black tracking-tighter opacity-60">≈ {{ $ro->formatted_base_amount }}</div>
                                        @endif
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
                                    <td class="px-6 py-4 text-right">
                                        <div class="text-[10px] text-gray-500 font-black uppercase tracking-tighter mb-1">{{ $ro->creator->name }}</div>
                                        <div class="text-[9px] text-gray-400 font-bold">{{ $ro->created_at->format('M d, Y') }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('request-orders.show', $ro) }}" class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-black rounded-xl hover:bg-indigo-600 hover:text-white transition-all duration-200 text-[10px] uppercase tracking-widest">
                                            Details
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-6 border-t border-gray-100 dark:border-gray-700 bg-gray-50/30 dark:bg-gray-900/20">
                    {{ $requestOrders->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
