<x-app-layout>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900 dark:text-gray-100">
                    {{-- Breadcrumbs --}}
                    <nav class="flex mb-6 text-gray-500 text-sm" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-3">
                            <li class="inline-flex items-center">
                                <a href="{{ route('products.index') }}" class="hover:text-indigo-600 transition">Products</a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 11 7.293 7.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                    <a href="{{ route('transactions.index') }}" class="hover:text-indigo-600 transition">Transactions</a>
                                </div>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 11 7.293 7.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">Record Movement</span>
                                </div>
                            </li>
                        </ol>
                    </nav>

                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h1 class="text-3xl font-extrabold tracking-tight">Record Transaction</h1>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Log a new stock movement for an existing product.</p>
                        </div>
                        <a href="{{ route('transactions.index') }}" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        </a>
                    </div>
                    
                    @include('partials.status-messages')
                    
                    <form action="{{ route('transactions.store') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="product_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Product</label>
                                <select name="product_id" id="product_id" class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition sm:text-sm p-3" required>
                                    <option value="">Select a Product</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ old('product_id', $selectedProductId) == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }} (Stock: {{ $product->stock }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('product_id')
                                    <p class="mt-2 text-sm text-rose-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="type" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Transaction Type</label>
                                <select name="type" id="type" class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition sm:text-sm p-3" required>
                                    <option value="Masuk" {{ old('type') == 'Masuk' ? 'selected' : '' }}>Masuk (Stock In)</option>
                                    <option value="Keluar" {{ old('type') == 'Keluar' ? 'selected' : '' }}>Keluar (Stock Out)</option>
                                </select>
                                @error('type')
                                    <p class="mt-2 text-sm text-rose-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="warehouse_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Warehouse / Branch</label>
                                <select name="warehouse_id" id="warehouse_id" class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition sm:text-sm p-3" required>
                                    <option value="">Select a Warehouse</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                            {{ $warehouse->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('warehouse_id')
                                    <p class="mt-2 text-sm text-rose-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div>
                            <label for="quantity" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Quantity</label>
                            <input type="number" name="quantity" id="quantity" value="{{ old('quantity', 1) }}" min="1" class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition sm:text-sm p-3" required>
                            @error('quantity')
                                <p class="mt-2 text-sm text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Description (Optional)</label>
                            <textarea name="description" id="description" rows="3" class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition sm:text-sm p-3" placeholder="e.g. Restock from supplier, Customer purchase, etc.">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-2 text-sm text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="pt-6">
                            <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition transform hover:-translate-y-0.5">
                                Record Transaction
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
