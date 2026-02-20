<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Payment History') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('reports.payments', ['format' => 'excel']) }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white font-bold rounded-xl hover:bg-emerald-700 shadow-sm transition text-xs uppercase tracking-widest">Excel</a>
                <a href="{{ route('reports.payments', ['format' => 'pdf']) }}" class="inline-flex items-center px-4 py-2 bg-rose-600 text-white font-bold rounded-xl hover:bg-rose-700 shadow-sm transition text-xs uppercase tracking-widest">PDF</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 dark:border-gray-700">
                <div class="p-0">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-6 py-4">Receipt #</th>
                                <th class="px-6 py-4">Invoice</th>
                                <th class="px-6 py-4">Amount</th>
                                <th class="px-6 py-4">Method</th>
                                <th class="px-6 py-4">Date</th>
                                <th class="px-6 py-4">Recorded By</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($payments as $payment)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                                    <td class="px-6 py-4 font-black">
                                        {{ $payment->payment_number }}
                                    </td>
                                    <td class="px-6 py-4 text-xs font-bold text-indigo-600 uppercase">
                                        <a href="{{ route('invoices.show', $payment->invoice) }}">{{ $payment->invoice->invoice_number }}</a>
                                    </td>
                                    <td class="px-6 py-4 font-black text-emerald-600">
                                        ${{ number_format($payment->amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-xs uppercase font-bold text-gray-500">
                                        {{ $payment->payment_method }}
                                    </td>
                                    <td class="px-6 py-4 text-xs font-bold text-gray-900 dark:text-white">
                                        {{ $payment->paid_at }}
                                    </td>
                                    <td class="px-6 py-4 text-xs text-gray-500 uppercase font-bold">
                                        {{ $payment->creator->name }}
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
