<x-app-layout>
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Create Product</h1>
        
        <div class="bg-white dark:bg-gray-800 p-6 rounded shadow">
            <form action="{{ route('products.store') }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label for="name" class="block text-gray-700 dark:text-gray-300">Name</label>
                    <input type="text" name="name" id="name" class="w-full mt-1 p-2 border rounded dark:bg-gray-700 dark:text-white" required>
                </div>
                
                <div class="mb-4">
                    <label for="price" class="block text-gray-700 dark:text-gray-300">Price</label>
                    <input type="number" name="price" id="price" class="w-full mt-1 p-2 border rounded dark:bg-gray-700 dark:text-white" step="0.01" required>
                </div>
                
                <div class="mb-4">
                    <label for="stock" class="block text-gray-700 dark:text-gray-300">Stock</label>
                    <input type="number" name="stock" id="stock" class="w-full mt-1 p-2 border rounded dark:bg-gray-700 dark:text-white" required>
                </div>
                
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Create</button>
            </form>
        </div>
    </div>
</x-app-layout>
