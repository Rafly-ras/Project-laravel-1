<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Add New Warehouse') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl p-8 border border-gray-100 dark:border-gray-700">
                <form action="{{ route('warehouses.store') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label for="name" class="block text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-widest mb-2">Warehouse Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-3" required placeholder="e.g. Main Warehouse, East Branch">
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="location" class="block text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-widest mb-2">Location / Address</label>
                        <input type="text" name="location" id="location" value="{{ old('location') }}" class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-3" placeholder="e.g. 123 Industrial St.">
                        @error('location')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <label for="is_active" class="ms-2 text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-widest">Active</label>
                    </div>

                    <div class="pt-6 flex items-center justify-end space-x-4">
                        <a href="{{ route('warehouses.index') }}" class="text-sm font-bold text-gray-500 hover:text-gray-700 uppercase tracking-widest transition">Cancel</a>
                        <button type="submit" class="px-6 py-3 bg-indigo-600 text-white font-black rounded-lg hover:bg-indigo-700 shadow-sm hover:shadow-md transition transform hover:-translate-y-0.5">
                            Create Warehouse
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
