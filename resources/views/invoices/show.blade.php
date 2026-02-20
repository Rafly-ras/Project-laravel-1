<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Invoice:') }} {{ $invoice->invoice_number }}
            </h2>
            <div class="flex gap-2">
                @can('invoices.export')
                    <a href="{{ route('invoices.pdf', $invoice) }}" class="px-4 py-2 bg-rose-600 text-white font-black rounded-lg text-xs uppercase tracking-widest hover:bg-rose-700 shadow-sm transition">Export PDF</a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('partials.status-messages')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Details & Payment History -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Items -->
                    <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl border border-gray-100 dark:border-gray-700">
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-6">Billed Items</h3>
                        <table class="w-full text-sm">
                            <thead class="border-b border-gray-100 dark:border-gray-700 text-[10px] font-black uppercase text-gray-400 tracking-widest">
                                <tr>
                                    <th class="py-4 text-left">Description</th>
                                    <th class="py-4 text-center">Qty</th>
                                    <th class="py-4 text-right">Unit Price</th>
                                    <th class="py-4 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                                @foreach($invoice->salesOrder->items as $item)
                                    <tr>
                                        <td class="py-4 font-bold text-gray-900 dark:text-white">{{ $item->product->name }}</td>
                                        <td class="py-4 text-center">{{ $item->qty }}</td>
                                        <td class="py-4 text-right text-gray-500">${{ number_format($item->price, 2) }}</td>
                                        <td class="py-4 text-right font-black text-gray-900 dark:text-white">${{ number_format($item->subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Payment History -->
                    <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl border border-gray-100 dark:border-gray-700">
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-6">Transactions</h3>
                        <div class="space-y-4">
                            @forelse($invoice->payments as $payment)
                                <div class="flex justify-between items-center p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl">
                                    <div class="flex items-center gap-4">
                                        <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 rounded-lg">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900 dark:text-white text-sm">Payment {{ $payment->payment_number }}</div>
                                            <div class="text-[10px] text-gray-500 font-bold uppercase">{{ $payment->payment_method }} • {{ $payment->reference_number ?? 'No Ref' }}</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-black text-emerald-600">${{ number_format($payment->amount, 2) }}</div>
                                        <div class="text-[10px] text-gray-400 font-bold">{{ $payment->paid_at }}</div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8 text-gray-400 font-bold uppercase text-[10px] tracking-widest">No payments recorded yet</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Summary & Payment Form -->
                <div class="space-y-8">
                    <!-- Balance Card -->
                    <div class="bg-indigo-600 p-8 rounded-2xl shadow-xl text-white">
                        <div class="text-[10px] font-black uppercase tracking-[0.2em] opacity-80 mb-2">Remaining Balance</div>
                        <div class="text-4xl font-black mb-6">${{ number_format($invoice->remaining_balance, 2) }}</div>
                        <div class="space-y-3 pt-6 border-t border-white/10">
                            <div class="flex justify-between text-xs font-bold">
                                <span class="opacity-80">Total Billed:</span>
                                <span>${{ number_format($invoice->total_amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-xs font-bold">
                                <span class="opacity-80">Total Paid:</span>
                                <span class="text-emerald-300">${{ number_format($invoice->paid_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Form -->
                    @if($invoice->status !== 'paid')
                        @can('payments.create')
                            <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl border border-gray-100 dark:border-gray-700">
                                <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-6">Record Payment</h3>
                                <form action="{{ route('payments.store') }}" method="POST" class="space-y-4">
                                    @csrf
                                    <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
                                    
                                    <div>
                                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-1">Amount</label>
                                        <input type="number" id="payment-amount" name="amount" value="{{ $invoice->remaining_balance }}" readonly required class="w-full bg-gray-100 dark:bg-gray-950 border-none rounded-xl text-gray-500 cursor-not-allowed font-bold">
                                    </div>

                                    <div>
                                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-1">Method</label>
                                        <select name="payment_method" required class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-indigo-500 font-bold">
                                            <option value="cash">Cash</option>
                                            <option value="transfer">Bank Transfer</option>
                                            <option value="e-wallet">E-Wallet</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-1">Ref #</label>
                                        <input type="text" id="reference-number" name="reference_number" readonly class="w-full bg-gray-100 dark:bg-gray-950 border-none rounded-xl text-gray-500 cursor-not-allowed font-bold">
                                    </div>

                                    <div>
                                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-1">Date</label>
                                        <input type="date" name="paid_at" value="{{ date('Y-m-d') }}" required class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-indigo-500 font-bold">
                                    </div>

                                    <button type="submit" class="w-full py-4 bg-emerald-600 text-white font-black rounded-xl hover:bg-emerald-700 transition uppercase tracking-widest text-xs shadow-md">Post Payment</button>
                                </form>
                            </div>
                        @endcan
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const refInput = document.getElementById('reference-number');

            // Generate auto-reference
            if (refInput && !refInput.value) {
                const randomPart = Math.random().toString(36).substring(2, 8).toUpperCase();
                const timestamp = Date.now().toString().slice(-4);
                refInput.value = `TRX-${timestamp}-${randomPart}`;
            }
        });
    </script>
    @endpush
</x-app-layout>
