<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h2 class="text-xl font-bold mb-4">{{ __("Low Stock Alerts") }}</h2>
                    
                    @if($lowStockProducts->count() > 0)
                        <div class="overflow-x-auto rounded-lg border border-rose-200 dark:border-rose-900/50">
                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-rose-700 uppercase bg-rose-50 dark:bg-rose-900/20 dark:text-rose-400">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">Product</th>
                                        <th scope="col" class="px-6 py-3">Category</th>
                                        <th scope="col" class="px-6 py-3">Stock</th>
                                        <th scope="col" class="px-6 py-3 text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-rose-100 dark:divide-rose-900/30">
                                    @foreach($lowStockProducts as $product)
                                        <tr class="bg-white dark:bg-gray-800 hover:bg-rose-50 dark:hover:bg-rose-900/10 transition">
                                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                                {{ $product->name }}
                                            </td>
                                            <td class="px-6 py-4">
                                                {{ $product->category->name ?? 'Uncategorized' }}
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800 dark:bg-rose-900 dark:text-rose-400">
                                                    {{ $product->stock }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <a href="{{ route('products.edit', $product) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 font-bold uppercase text-xs tracking-wider">
                                                    Restock
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="bg-emerald-50 dark:bg-emerald-900/20 p-4 rounded-lg flex items-center text-emerald-700 dark:text-emerald-400">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            <span>All products are well-stocked!</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
