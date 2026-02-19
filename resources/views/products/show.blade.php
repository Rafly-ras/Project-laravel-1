<x-app-layout>
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Product Details</h1>
        
        <div class="bg-white dark:bg-gray-800 p-6 rounded shadow text-gray-900 dark:text-gray-300">
            <div class="mb-4">
                <strong>Name:</strong> {{ $product->name }}
            </div>
            <div class="mb-4">
                <strong>Price:</strong> {{ number_format($product->price, 2) }}
            </div>
            <div class="mb-4">
                <strong>Stock:</strong> {{ $product->stock }}
            </div>
            
            <a href="{{ route('products.index') }}" class="text-blue-600 hover:text-blue-800">Back to List</a>
        </div>
    </div>
</x-app-layout>
