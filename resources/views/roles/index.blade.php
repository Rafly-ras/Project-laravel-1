<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Roles & Permissions') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 dark:border-gray-700">
                <div class="p-0">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700/50 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-4">Role Name</th>
                                <th scope="col" class="px-6 py-4 text-center">Users</th>
                                <th scope="col" class="px-6 py-4 text-center">Permissions</th>
                                <th scope="col" class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($roles as $role)
                                <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition duration-150">
                                    <td class="px-6 py-4 font-black text-gray-900 dark:text-white uppercase tracking-widest text-xs">
                                        {{ $role->name }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-md font-bold">
                                            {{ $role->users_count }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-2 py-0.5 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 rounded-md font-bold">
                                            {{ $role->permissions_count }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('roles.edit', $role) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-black text-[10px] text-white uppercase tracking-widest hover:bg-indigo-700 shadow-sm hover:shadow-indigo-500/20 transition duration-150">
                                            Manage Permissions
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <p class="mt-4 text-xs text-gray-500 dark:text-gray-400 font-bold uppercase tracking-widest text-center">
                <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                Roles are fixed for this system. You can only manage the permissions assigned to them.
            </p>
        </div>
    </div>
</x-app-layout>
