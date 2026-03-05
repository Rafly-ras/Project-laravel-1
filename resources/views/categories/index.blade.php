<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Product Categories') }}
            </h2>
            <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'create-category')" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-bold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                + Add Category
            </button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('partials.status-messages')

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 dark:border-gray-700">
                <div class="p-0">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-6 py-4">Name</th>
                                <th class="px-6 py-4">Products Count</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($categories as $category)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                                    <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">
                                        {{ $category->name }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-500">
                                        {{ $category->products_count }} items
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <button 
                                            x-data="" 
                                            x-on:click.prevent="$dispatch('open-modal', 'edit-category-{{ $category->id }}')"
                                            class="text-indigo-600 hover:text-indigo-900 font-bold"
                                        >
                                            Edit
                                        </button>
                                        
                                        <form action="{{ route('categories.destroy', $category) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-600 hover:text-rose-900 font-bold" onclick="return confirm('Are you sure? This cannot be undone.')">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                {{-- Edit Modal for each category --}}
                                <x-modal name="edit-category-{{ $category->id }}" focusable>
                                    <form method="post" action="{{ route('categories.update', $category) }}" class="p-6">
                                        @csrf
                                        @method('PATCH')
                                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Edit Category</h2>
                                        <div class="mt-6">
                                            <x-input-label for="name-{{ $category->id }}" value="Category Name" class="sr-only" />
                                            <x-text-input id="name-{{ $category->id }}" name="name" type="text" class="mt-1 block w-full" value="{{ $category->name }}" required />
                                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                        </div>
                                        <div class="mt-6 flex justify-end">
                                            <x-secondary-button x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                                            <x-primary-button class="ms-3">Update Category</x-primary-button>
                                        </div>
                                    </form>
                                </x-modal>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-10 text-center text-gray-400">No categories found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($categories->hasPages())
                    <div class="p-6 border-t border-gray-100 dark:border-gray-700">
                        {{ $categories->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Create Modal --}}
    <x-modal name="create-category" focusable>
        <form method="post" action="{{ route('categories.store') }}" class="p-6">
            @csrf
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Create New Category</h2>
            <div class="mt-6">
                <x-input-label for="name" value="Category Name" class="sr-only" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" placeholder="Category Name" required />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>
            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                <x-primary-button class="ms-3">Save Category</x-primary-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
