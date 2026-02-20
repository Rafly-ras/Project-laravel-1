<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Expense Categories') }}
            </h2>
            <button onclick="openModal('addCategoryModal')" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white font-black rounded-xl hover:bg-indigo-700 shadow-md transition transform hover:-translate-y-0.5 text-xs uppercase tracking-widest">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Add Category
            </button>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-6 py-4">Name</th>
                            <th class="px-6 py-4">Description</th>
                            <th class="px-6 py-4 text-center">Expenses Count</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($categories as $category)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                                <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">{{ $category->name }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ $category->description ?? '-' }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2 py-1 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 rounded-lg font-bold">
                                        {{ $category->expenses_count }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <button onclick="editCategory({{ $category->id }}, '{{ $category->name }}', '{{ $category->description }}')" class="text-indigo-600 hover:text-indigo-900 font-bold uppercase text-xs">Edit</button>
                                    <form action="{{ route('expense-categories.destroy', $category) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" onclick="return confirm('Are you sure?')" class="text-rose-600 hover:text-rose-900 font-bold uppercase text-xs">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Modal -->
    <div id="addCategoryModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-3xl p-8 max-w-md w-full shadow-2xl">
            <h3 class="text-xl font-black text-gray-900 dark:text-white mb-6">New Expense Category</h3>
            <form action="{{ route('expense-categories.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-black uppercase text-gray-500 tracking-widest mb-1">Category Name</label>
                        <input type="text" name="name" required class="w-full rounded-xl border-gray-200 dark:bg-gray-700 dark:border-gray-600">
                    </div>
                    <div>
                        <label class="block text-xs font-black uppercase text-gray-500 tracking-widest mb-1">Description</label>
                        <textarea name="description" class="w-full rounded-xl border-gray-200 dark:bg-gray-700 dark:border-gray-600"></textarea>
                    </div>
                </div>
                <div class="mt-8 flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('addCategoryModal')" class="px-6 py-2 text-gray-500 font-bold uppercase text-xs">Cancel</button>
                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white font-black rounded-xl uppercase text-xs shadow-md">Create Category</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editCategoryModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-3xl p-8 max-w-md w-full shadow-2xl">
            <h3 class="text-xl font-black text-gray-900 dark:text-white mb-6">Edit Category</h3>
            <form id="editForm" method="POST">
                @csrf @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-black uppercase text-gray-500 tracking-widest mb-1">Category Name</label>
                        <input type="text" id="edit_name" name="name" required class="w-full rounded-xl border-gray-200 dark:bg-gray-700 dark:border-gray-600">
                    </div>
                    <div>
                        <label class="block text-xs font-black uppercase text-gray-500 tracking-widest mb-1">Description</label>
                        <textarea id="edit_description" name="description" class="w-full rounded-xl border-gray-200 dark:bg-gray-700 dark:border-gray-600"></textarea>
                    </div>
                </div>
                <div class="mt-8 flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('editCategoryModal')" class="px-6 py-2 text-gray-500 font-bold uppercase text-xs">Cancel</button>
                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white font-black rounded-xl uppercase text-xs shadow-md">Update Category</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
        function closeModal(id) { document.getElementById(id).classList.add('hidden'); }
        function editCategory(id, name, desc) {
            document.getElementById('editForm').action = `/finance/expense-categories/${id}`;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_description').value = desc;
            openModal('editCategoryModal');
        }
    </script>
</x-app-layout>
