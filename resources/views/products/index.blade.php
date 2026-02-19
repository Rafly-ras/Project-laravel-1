<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
                        <h1 class="text-3xl font-extrabold tracking-tight">Products</h1>
                        <div class="flex gap-2">
                            <a href="{{ route('products.stock-summary') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 focus:outline-none transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                Stock Summary
                            </a>
                            <a href="{{ route('products.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Add Product
                            </a>
                        </div>
                    </div>

                    {{-- Search & Filters --}}
                    <div class="mb-8 p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                        <form action="{{ route('products.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Search</label>
                                <input type="text" name="name" id="name" value="{{ request('name') }}" placeholder="Product name..." class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="min_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Min Price</label>
                                <input type="number" name="min_price" id="min_price" value="{{ request('min_price') }}" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="max_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Max Price</label>
                                <input type="number" name="max_price" id="max_price" value="{{ request('max_price') }}" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            <div class="flex items-end">
                                <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Filter
                                </button>
                            </div>
                        </form>
                    </div>

                    @include('partials.status-messages')

                    <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Product Name</th>
                                    <th scope="col" class="px-6 py-3">Category</th>
                                    <th scope="col" class="px-6 py-3">Price</th>
                                    <th scope="col" class="px-6 py-3">Stock Status</th>
                                    <th scope="col" class="px-6 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($products as $product)
                                <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $product->name }}</td>
                                    <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                        {{ $product->category->name ?? 'Uncategorized' }}
                                    </td>
                                    <td class="px-6 py-4">${{ number_format($product->price, 2) }}</td>
                                    <td class="px-6 py-4">
                                        @php
                                            $stockClass = 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400';
                                            $stockLabel = 'In Stock';
                                            if ($product->stock <= 0) {
                                                $stockClass = 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-400';
                                                $stockLabel = 'Out of Stock';
                                            } elseif ($product->stock <= 5) {
                                                $stockClass = 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400';
                                                $stockLabel = 'Low Stock';
                                            }
                                        @endphp
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $stockClass }}">
                                            {{ $stockLabel }} ({{ $product->stock }})
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-1">
                                        <a href="{{ route('products.transactions.create', $product) }}" class="inline-flex items-center p-2 text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 rounded-lg transition" title="Add Transaction">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                        </a>
                                        <a href="{{ route('products.show', $product) }}" class="inline-flex items-center p-2 text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-lg transition" title="View Details">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </a>
                                        <a href="{{ route('products.edit', $product) }}" class="inline-flex items-center p-2 text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 rounded-lg transition" title="Edit Product">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>
                                        <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center p-2 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-lg transition" title="Delete Product" onclick="return confirm('Are you sure?')">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                        </svg>
                                        <p class="mt-2 text-sm">No products found matching your criteria.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $products->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
