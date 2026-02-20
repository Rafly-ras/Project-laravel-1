<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Request Order:') }} {{ $requestOrder->request_number }}
            </h2>
            <div class="flex gap-2">
                @if($requestOrder->status === 'draft')
                    @can('ro.approve')
                        <form action="{{ route('request-orders.approve', $requestOrder) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white font-black rounded-lg text-xs uppercase tracking-widest hover:bg-emerald-700 transition">Approve</button>
                        </form>
                        <form action="{{ route('request-orders.reject', $requestOrder) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-rose-600 text-white font-black rounded-lg text-xs uppercase tracking-widest hover:bg-rose-700 transition">Reject</button>
                        </form>
                    @endcan
                @endif

                @if($requestOrder->status === 'approved')
                    @can('ro.convert')
                        <form action="{{ route('request-orders.convert', $requestOrder) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white font-black rounded-lg text-xs uppercase tracking-widest hover:bg-indigo-700 transition">Convert to Sales Order</button>
                        </form>
                    @endcan
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('partials.status-messages')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Details -->
                <div class="lg:col-span-2 space-y-8">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 dark:border-gray-700">
                        <div class="p-8">
                            <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-6">Order Items</h3>
                            <table class="w-full text-sm text-left">
                                <thead class="border-b border-gray-100 dark:border-gray-700 text-xs font-bold text-gray-500 uppercase tracking-widest">
                                    <tr>
                                        <th class="py-4">Product</th>
                                        <th class="py-4 text-center">Qty</th>
                                        <th class="py-4 text-right">Price</th>
                                        <th class="py-4 text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                                    @foreach($requestOrder->items as $item)
                                        <tr>
                                            <td class="py-4 font-bold text-gray-900 dark:text-white">{{ $item->product->name }}</td>
                                            <td class="py-4 text-center font-bold">{{ $item->qty }}</td>
                                            <td class="py-4 text-right text-gray-500">${{ number_format($item->price, 2) }}</td>
                                            <td class="py-4 text-right font-black text-gray-900 dark:text-white">${{ number_format($item->subtotal, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="border-t-2 border-gray-100 dark:border-gray-700">
                                        <td colspan="3" class="py-6 text-right font-black uppercase text-xs tracking-widest text-gray-500">Total Amount</td>
                                        <td class="py-6 text-right font-black text-2xl text-indigo-600">${{ number_format($requestOrder->total_amount, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Information -->
                <div class="space-y-8">
                    <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-6">Order Status</h3>
                        <div class="mb-8">
                            @php
                                $statusColors = [
                                    'draft' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                    'approved' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400',
                                    'rejected' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-400',
                                    'converted' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400',
                                ];
                            @endphp
                            <span class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest {{ $statusColors[$requestOrder->status] }}">
                                {{ $requestOrder->status }}
                            </span>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <label class="block text-[10px] font-black uppercase text-gray-400 tracking-widest mb-1">Customer</label>
                                <div class="font-bold text-gray-900 dark:text-white">{{ $requestOrder->customer_name }}</div>
                                <div class="text-xs text-gray-500">{{ $requestOrder->customer_email ?? 'No email' }}</div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black uppercase text-gray-400 tracking-widest mb-1">Created By</label>
                                <div class="font-bold text-gray-900 dark:text-white text-sm">{{ $requestOrder->creator->name }}</div>
                                <div class="text-[10px] text-gray-500 font-bold uppercase">{{ $requestOrder->created_at->format('M d, Y H:i') }}</div>
                            </div>
                            @if($requestOrder->approved_by)
                                <div>
                                    <label class="block text-[10px] font-black uppercase text-gray-400 tracking-widest mb-1">Approved By</label>
                                    <div class="font-bold text-gray-900 dark:text-white text-sm">{{ $requestOrder->approver->name }}</div>
                                    <div class="text-[10px] text-gray-500 font-bold uppercase">{{ $requestOrder->approved_at->format('M d, Y H:i') }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
