<x-app-layout>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900 dark:text-gray-100">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h1 class="text-3xl font-extrabold tracking-tight">Edit Product</h1>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Update the information for <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ $product->name }}</span>.</p>
                        </div>
                        <a href="{{ route('products.index') }}" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        </a>
                    </div>
                    
                    @include('partials.status-messages')
                    
                    <form action="{{ route('products.update', $product) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Product Name</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition sm:text-sm p-3" required>
                                @error('name')
                                    <p class="mt-2 text-sm text-rose-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="category_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Category</label>
                                <select name="category_id" id="category_id" class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition sm:text-sm p-3">
                                    <option value="">Select a Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <p class="mt-2 text-sm text-rose-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="price" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Price ($)</label>
                                <div class="relative rounded-lg shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input type="number" name="price" id="price" value="{{ old('price', $product->price) }}" step="0.01" class="block w-full pl-7 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition sm:text-sm p-3" required>
                                </div>
                                @error('price')
                                    <p class="mt-2 text-sm text-rose-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="stock" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Inventory Stock</label>
                                <input type="number" name="stock" id="stock" value="{{ old('stock', $product->stock) }}" class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition sm:text-sm p-3" required>
                                @error('stock')
                                    <p class="mt-2 text-sm text-rose-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="pt-6 flex gap-4">
                            <a href="{{ route('products.index') }}" class="flex-1 inline-flex justify-center py-3 px-4 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm text-sm font-bold text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                                Cancel
                            </a>
                            <button type="submit" class="flex-[2] inline-flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition transform hover:-translate-y-0.5">
                                Update Product
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
