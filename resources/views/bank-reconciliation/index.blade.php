@extends('layouts.app')

@section('title', 'Bank Reconciliation')

@section('content')
<div class="p-6">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Bank Reconciliation</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Reconcile bank statements against your General Ledger</p>
        </div>
        <button onclick="document.getElementById('import-modal').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-all duration-200 shadow-md shadow-indigo-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
            Import CSV
        </button>
    </div>

    {{-- Statement Batches Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                    <th class="text-left px-6 py-4 font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-xs">Date</th>
                    <th class="text-left px-6 py-4 font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-xs">Bank Account</th>
                    <th class="text-left px-6 py-4 font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-xs">Filename</th>
                    <th class="text-left px-6 py-4 font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-xs">Status</th>
                    <th class="text-left px-6 py-4 font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-xs">Imported By</th>
                    <th class="px-6 py-4"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                @forelse($statements as $stmt)
                <tr class="hover:bg-indigo-50/30 dark:hover:bg-gray-700/30 transition-colors">
                    <td class="px-6 py-4 text-gray-900 dark:text-white font-medium">
                        {{ $stmt->statement_date->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4 text-gray-700 dark:text-gray-300">{{ $stmt->bankAccount->name ?? '-' }}</td>
                    <td class="px-6 py-4 text-gray-500 dark:text-gray-400 font-mono text-xs">{{ $stmt->filename }}</td>
                    <td class="px-6 py-4">
                        @php
                            $colors = [
                                'pending'    => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                                'processing' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                                'completed'  => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400',
                                'failed'     => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                            ];
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold capitalize {{ $colors[$stmt->status] ?? '' }}">
                            {{ $stmt->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-500 dark:text-gray-400">{{ $stmt->importer->name ?? '-' }}</td>
                    <td class="px-6 py-4 text-right">
                        @if($stmt->status === 'completed')
                        <a href="{{ route('bank-recon.show', $stmt) }}"
                           class="text-indigo-600 dark:text-indigo-400 hover:underline text-xs font-semibold">
                            View & Match →
                        </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center text-gray-400 dark:text-gray-500">
                        <svg class="w-10 h-10 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        No bank statements imported yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
            {{ $statements->links() }}
        </div>
    </div>
</div>

{{-- Import Modal --}}
<div id="import-modal" class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg">
        <div class="flex items-center justify-between p-6 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white">Import Bank Statement</h2>
            <button onclick="document.getElementById('import-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('bank-recon.import') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bank Account</label>
                <select name="bank_account_id" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm" required>
                    <option value="">Select bank account...</option>
                    {{-- Populated from controller: accounts with code 11xx --}}
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Statement Date</label>
                <input type="date" name="statement_date" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">CSV File</label>
                <div class="mt-1 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-6 text-center hover:border-indigo-400 transition-colors">
                    <input type="file" name="csv_file" accept=".csv,.txt" class="hidden" id="csv-upload">
                    <label for="csv-upload" class="cursor-pointer">
                        <svg class="w-8 h-8 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Click to select CSV file</p>
                    </label>
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('import-modal').classList.add('hidden')"
                        class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">Cancel</button>
                <button type="submit"
                        class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold transition-colors shadow-md shadow-indigo-200">
                    Upload & Process
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
