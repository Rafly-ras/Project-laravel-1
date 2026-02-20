<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Sales Order:') }} {{ $salesOrder->sales_number }}
            </h2>
            <div class="flex gap-2">
                @if($salesOrder->status === 'draft')
                    @can('so.confirm')
                        <form action="{{ route('sales-orders.confirm', $salesOrder) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white font-black rounded-lg text-xs uppercase tracking-widest hover:bg-emerald-700 shadow-sm transition">Confirm Order & Update Stock</button>
                        </form>
                    @endcan
                @endif

                @if($salesOrder->status === 'confirmed')
                    @can('invoices.create')
                        <form action="{{ route('sales-orders.invoice', $salesOrder) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white font-black rounded-lg text-xs uppercase tracking-widest hover:bg-indigo-700 shadow-sm transition">Generate Invoice</button>
                        </form>
                    @endcan
                @endif

                @if($salesOrder->invoice)
                    <a href="{{ route('invoices.show', $salesOrder->invoice) }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 font-black rounded-lg text-xs uppercase tracking-widest hover:bg-gray-200 transition">View Invoice</a>
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
                                        <th class="py-4">Warehouse</th>
                                        <th class="py-4 text-center">Qty</th>
                                        <th class="py-4 text-right">Price</th>
                                        <th class="py-4 text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                                    @foreach($salesOrder->items as $item)
                                        <tr>
                                            <td class="py-4 font-bold text-gray-900 dark:text-white">{{ $item->product->name }}</td>
                                            <td class="py-4 font-bold text-xs uppercase text-gray-500">{{ $item->warehouse->name }}</td>
                                            <td class="py-4 text-center font-bold">{{ $item->qty }}</td>
                                            <td class="py-4 text-right text-gray-500">${{ number_format($item->price, 2) }}</td>
                                            <td class="py-4 text-right font-black text-gray-900 dark:text-white">${{ number_format($item->subtotal, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="border-t-2 border-gray-100 dark:border-gray-700">
                                        <td colspan="4" class="py-6 text-right font-black uppercase text-xs tracking-widest text-gray-500">Total amount</td>
                                        <td class="py-6 text-right font-black text-2xl text-indigo-600">${{ number_format($salesOrder->total_amount, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Info Sidebar -->
                <div class="space-y-8">
                    <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl border border-gray-100 dark:border-gray-700">
                         <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-6">SO Status</h3>
                         <div class="mb-8">
                            @php
                                $statusColors = [
                                    'draft' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                    'confirmed' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                                    'delivered' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400',
                                    'invoiced' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400',
                                    'cancelled' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-400',
                                ];
                            @endphp
                            <span class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest {{ $statusColors[$salesOrder->status] }}">
                                {{ $salesOrder->status }}
                            </span>
                         </div>

                         <div class="space-y-6">
                            <div>
                                <label class="block text-[10px] font-black uppercase text-gray-400 tracking-widest mb-1">Customer</label>
                                <div class="font-bold text-gray-900 dark:text-white">{{ $salesOrder->customer_name }}</div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black uppercase text-gray-400 tracking-widest mb-1">Reference RO</label>
                                <div class="font-bold text-indigo-600 text-sm">
                                    @if($salesOrder->requestOrder)
                                        <a href="{{ route('request-orders.show', $salesOrder->requestOrder) }}">{{ $salesOrder->requestOrder->request_number }}</a>
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black uppercase text-gray-400 tracking-widest mb-1">Stock Impact</label>
                                <div class="text-[10px] font-bold {{ $salesOrder->confirmed_at ? 'text-emerald-600' : 'text-rose-500' }} uppercase tracking-widest">
                                    {{ $salesOrder->confirmed_at ? '✓ Stock Reduced at ' . $salesOrder->confirmed_at->format('M d, H:i') : '⚠ Stock not yet impacted' }}
                                </div>
                            </div>
                         </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
