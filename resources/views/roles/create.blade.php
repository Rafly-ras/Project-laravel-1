<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create New Role') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('roles.store') }}" method="POST">
                @csrf
                
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-2xl p-8 border border-gray-100 dark:border-gray-700 mb-6">
                    <div>
                        <label for="name" class="block text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-widest mb-2">Role Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-3" required placeholder="e.g. Warehouse Staff, Manager">
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    @foreach($permissions as $module => $modulePermissions)
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden border border-gray-100 dark:border-gray-700">
                            <div class="p-4 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700">
                                <h3 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest">{{ $module }}</h3>
                            </div>
                            <div class="p-4 space-y-3">
                                @foreach($modulePermissions as $permission)
                                    <label class="flex items-center group cursor-pointer">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" 
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 transition">
                                        <span class="ms-2 text-sm text-gray-600 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white transition">{{ $permission->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="flex items-center justify-end space-x-4">
                    <a href="{{ route('roles.index') }}" class="text-sm font-bold text-gray-500 hover:text-gray-700 uppercase tracking-widest transition">Cancel</a>
                    <button type="submit" class="px-6 py-3 bg-indigo-600 text-white font-black rounded-lg hover:bg-indigo-700 shadow-sm hover:shadow-md transition transform hover:-translate-y-0.5">
                        Create Role
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
