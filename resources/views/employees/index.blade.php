<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Team Members') }}
            </h2>
            <a href="{{ route('employees.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-bold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Add Member
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 dark:border-gray-700">
                <div class="p-0">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700/50 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-4">Name</th>
                                <th scope="col" class="px-6 py-4">Email</th>
                                <th scope="col" class="px-6 py-4 text-center">Role</th>
                                <th scope="col" class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($employees as $employee)
                                <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition duration-150">
                                    <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">
                                        {{ $employee->name }}
                                        @if($employee->id === auth()->id())
                                            <span class="ml-2 px-2 py-0.5 bg-indigo-100 text-indigo-800 text-[10px] rounded-full uppercase">You</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $employee->email }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @php
                                            $roleColor = match($employee->role->name ?? 'None') {
                                                'Admin' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400',
                                                'Staff' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400',
                                                'Viewer' => 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400',
                                                default => 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-400',
                                            };
                                        @endphp
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-black {{ $roleColor }}">
                                            {{ $employee->role->name ?? 'No Role' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <a href="{{ route('employees.edit', $employee) }}" class="inline-flex items-center p-2 text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-lg transition" title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>
                                        @if($employee->id !== auth()->id())
                                            <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center p-2 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-lg transition" title="Delete" onclick="return confirm('Are you sure you want to remove this member?')">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($employees->hasPages())
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/30 border-t border-gray-100 dark:border-gray-700">
                        {{ $employees->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
