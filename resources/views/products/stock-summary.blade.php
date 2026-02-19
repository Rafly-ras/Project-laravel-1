<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
                        <div>
                            <h1 class="text-3xl font-extrabold tracking-tight">Stock Summary Report</h1>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Overview of inventory distribution and valuation per category.</p>
                        </div>
                        <a href="{{ route('products.index') }}" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition">
                            Back to Inventory
                        </a>
                    </div>

                    <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left font-bold">Category</th>
                                    <th scope="col" class="px-6 py-3 text-center font-bold">Total Products</th>
                                    <th scope="col" class="px-6 py-3 text-center font-bold">Total Stock</th>
                                    <th scope="col" class="px-6 py-3 text-right font-bold">Total Value</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($summary as $item)
                                <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                    <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">
                                        {{ $item->name }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        {{ $item->products_count }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $item->total_stock <= 10 ? 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-400' : 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400' }}">
                                            {{ number_format($item->total_stock ?? 0) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right font-bold text-gray-900 dark:text-white">
                                        ${{ number_format($item->total_value ?? 0, 2) }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                        No data available.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-6 py-4 text-left font-extrabold uppercase text-gray-900 dark:text-white">Grand Total</th>
                                    <th class="px-6 py-4 text-center font-extrabold text-gray-900 dark:text-white">{{ $summary->sum('products_count') }}</th>
                                    <th class="px-6 py-4 text-center font-extrabold text-gray-900 dark:text-white text-indigo-600 dark:text-indigo-400">{{ number_format($summary->sum('total_stock')) }}</th>
                                    <th class="px-6 py-4 text-right font-extrabold text-emerald-600 dark:text-emerald-400 text-lg">${{ number_format($summary->sum('total_value'), 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
