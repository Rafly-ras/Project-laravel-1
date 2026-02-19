<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Warehouses / Branches') }}
            </h2>
            <a href="{{ route('warehouses.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-bold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                + Add Warehouse
            </a>
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
                                <th class="px-6 py-4">Location</th>
                                <th class="px-6 py-4">Products</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($warehouses as $warehouse)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                                    <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">
                                        {{ $warehouse->name }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-500">
                                        {{ $warehouse->location ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-500">
                                        {{ $warehouse->products_count }} items
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase {{ $warehouse->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $warehouse->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('warehouses.edit', $warehouse) }}" class="text-indigo-600 hover:text-indigo-900 font-bold">Edit</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-400">No warehouses found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($warehouses->hasPages())
                    <div class="p-6 border-t border-gray-100 dark:border-gray-700">
                        {{ $warehouses->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
