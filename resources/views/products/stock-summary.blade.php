<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Stock Summary Report') }}
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('products.export.csv') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white font-bold rounded-lg hover:bg-emerald-700 transition text-xs uppercase tracking-widest">
                    Export CSV
                </a>
                <a href="{{ route('products.export.pdf') }}" class="inline-flex items-center px-4 py-2 bg-rose-600 text-white font-bold rounded-lg hover:bg-rose-700 transition text-xs uppercase tracking-widest">
                    Export PDF
                </a>
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
                                <th class="px-6 py-4">Product</th>
                                <th class="px-6 py-4">Total Stock</th>
                                <th class="px-6 py-4">Value</th>
                                <th class="px-6 py-4">Per Warehouse</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($products as $product)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900 dark:text-white">{{ $product->name }}</div>
                                        <div class="text-xs text-gray-500 tracking-wider uppercase">{{ $product->category->name ?? 'Uncategorized' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="font-black {{ $product->stock <= 5 ? 'text-rose-600' : 'text-gray-900 dark:text-white' }}">
                                            {{ $product->stock }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 font-bold text-emerald-600">
                                        ${{ number_format($product->price * $product->stock, 2) }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="space-y-1">
                                            @forelse($product->warehouses as $wh)
                                                <div class="text-[10px] flex justify-between">
                                                    <span class="text-gray-500 font-bold uppercase">{{ $wh->name }}:</span>
                                                    <span class="text-gray-900 dark:text-white font-black">{{ $wh->pivot->stock }}</span>
                                                </div>
                                            @empty
                                                <span class="text-[10px] text-gray-400 italic font-bold">No Warehouse Assigned</span>
                                            @endforelse
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-6 border-t border-gray-100 dark:border-gray-700">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
