<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <a href="{{ route('roles.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Manage Permissions') }}: <span class="uppercase tracking-widest text-indigo-600 dark:text-indigo-400">{{ $role->name }}</span>
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 dark:border-gray-700">
                <div class="p-8">
                    <form action="{{ route('roles.update', $role) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="space-y-4">
                            <p class="text-sm font-black text-gray-500 uppercase tracking-widest mb-6 border-b border-gray-50 pb-4 dark:border-gray-700">Available Permissions</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($permissions as $permission)
                                    <label class="relative flex items-center p-4 rounded-xl border-2 {{ in_array($permission->id, $rolePermissions) ? 'border-indigo-100 bg-indigo-50/30 dark:border-indigo-900/30 dark:bg-indigo-900/10' : 'border-gray-50 dark:border-gray-700/50' }} hover:border-indigo-400 dark:hover:border-indigo-600 transition cursor-pointer group">
                                        <div class="flex items-center h-5">
                                            <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }} class="h-5 w-5 text-indigo-600 border-gray-300 rounded-md focus:ring-indigo-500 transition">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <span class="block font-bold text-gray-900 dark:text-white group-hover:text-indigo-600 transition">{{ $permission->name }}</span>
                                            <span class="text-xs text-gray-500 font-mono">{{ $permission->slug }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-10 space-x-4 border-t border-gray-50 dark:border-gray-700 pt-8">
                            <a href="{{ route('roles.index') }}" class="text-sm font-bold text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition">Cancel</a>
                            <button type="submit" class="inline-flex items-center px-8 py-3 bg-indigo-600 border border-transparent rounded-xl font-black text-xs text-white uppercase tracking-widest hover:bg-indigo-700 shadow-lg hover:shadow-indigo-500/30 transition duration-150">
                                Save Permissions
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            @if($role->name === 'Admin')
                <div class="mt-6 p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-900/30 rounded-xl">
                    <div class="flex">
                        <svg class="h-5 w-5 text-amber-600 dark:text-amber-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <p class="text-xs text-amber-800 dark:text-amber-300 font-bold uppercase tracking-widest">
                            Warning: Modifying Admin permissions can restrict system-wide oversight. Use caution.
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
