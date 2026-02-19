<x-app-layout>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-2xl">
                <div class="p-8">
                    <div class="flex items-center justify-between mb-8">
                        <h1 class="text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white">Product Details</h1>
                        <a href="{{ route('products.index') }}" class="inline-flex items-center text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                            Back to inventory
                        </a>
                    </div>

                    @include('partials.status-messages')

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-2xl p-6 mb-8 border border-gray-100 dark:border-gray-600">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-6">
                                <div>
                                    <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Product Name</label>
                                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $product->name }}</p>
                                </div>
                                <div>
                                    <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Category</label>
                                    <p class="text-lg font-semibold text-gray-700 dark:text-gray-300">{{ $product->category->name ?? 'Uncategorized' }}</p>
                                </div>
                                <div>
                                    <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Pricing</label>
                                    <p class="text-2xl font-black text-indigo-600 dark:text-indigo-400">${{ number_format($product->price, 2) }}</p>
                                </div>
                            </div>
                            <div class="space-y-6">
                                <div>
                                    <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Stock Level</label>
                                    <div class="mt-1 flex items-center">
                                        @if($product->stock > 10)
                                            <div class="h-3 w-3 rounded-full bg-emerald-500 mr-2"></div>
                                            <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $product->stock }} (Available)</span>
                                        @elseif($product->stock > 0)
                                            <div class="h-3 w-3 rounded-full bg-amber-500 mr-2"></div>
                                            <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $product->stock }} (Low)</span>
                                        @else
                                            <div class="h-3 w-3 rounded-full bg-rose-500 mr-2"></div>
                                            <span class="text-lg font-bold text-gray-900 dark:text-white">Out of Stock</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <a href="{{ route('products.edit', $product) }}" class="flex-1 inline-flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-amber-500 hover:bg-amber-600 transition transform hover:-translate-y-0.5">
                            Edit Product
                        </a>
                        <form action="{{ route('products.destroy', $product) }}" method="POST" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full inline-flex justify-center items-center py-3 px-4 border border-rose-200 dark:border-rose-900 rounded-lg shadow-sm text-sm font-bold text-rose-600 dark:text-rose-400 bg-white dark:bg-gray-800 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition transform hover:-translate-y-0.5" onclick="return confirm('Delete this product permanently?')">
                                Delete Item
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
