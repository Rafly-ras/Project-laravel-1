@extends('layouts.app')

@section('title', 'Match Transactions')

@section('content')
<div class="p-6" x-data="{ selectedLine: null, selectedJournal: null, difference: 0 }">
    {{-- Header --}}
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('bank-recon.index') }}" class="text-gray-400 hover:text-indigo-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Match Transactions</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                Bank Account: <strong>{{ $bankStatement->bankAccount->name }}</strong> &bull;
                Statement Date: <strong>{{ $bankStatement->statement_date->format('d M Y') }}</strong>
            </p>
        </div>
    </div>

    {{-- Progress Summary --}}
    @php
        $total       = $lines->total();
        $reconciled  = $bankStatement->lines()->where('status', 'reconciled')->count();
        $pct         = $total > 0 ? round(($reconciled / $total) * 100) : 0;
    @endphp
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-5 mb-6 flex items-center gap-6">
        <div class="flex-1">
            <div class="flex justify-between text-sm mb-2">
                <span class="font-medium text-gray-700 dark:text-gray-300">Reconciliation Progress</span>
                <span class="font-bold text-indigo-600">{{ $reconciled }} / {{ $total }} ({{ $pct }}%)</span>
            </div>
            <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2">
                <div class="bg-indigo-600 h-2 rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
            </div>
        </div>
    </div>

    {{-- Side-by-side Matching UI --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Left: Bank Statement Lines --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="font-bold text-gray-900 dark:text-white text-sm">Bank Statement Lines</h2>
            </div>
            <div class="divide-y divide-gray-50 dark:divide-gray-700 max-h-[60vh] overflow-y-auto">
                @forelse($lines as $line)
                <div @click="selectedLine = {{ $line->id }}"
                     :class="selectedLine === {{ $line->id }} ? 'bg-indigo-50 dark:bg-indigo-900/20 border-l-4 border-indigo-500' : 'hover:bg-gray-50 dark:hover:bg-gray-700/30'"
                     class="px-5 py-3 cursor-pointer transition-all">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate max-w-[220px]">{{ $line->description }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $line->transaction_date->format('d M Y') }}
                                @if($line->reference) &bull; <span class="font-mono">{{ $line->reference }}</span> @endif
                            </p>
                        </div>
                        <div class="text-right flex-shrink-0 ml-3">
                            <p class="text-sm font-bold {{ $line->credit > 0 ? 'text-emerald-600' : 'text-red-500' }}">
                                {{ $line->credit > 0 ? '+' : '-' }}{{ number_format(abs($line->amount), 2) }}
                            </p>
                            @php $tc = ['unmatched'=>'bg-gray-100 text-gray-600','suggested'=>'bg-yellow-100 text-yellow-700','reconciled'=>'bg-emerald-100 text-emerald-700'] @endphp
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $tc[$line->status] ?? '' }}">{{ $line->status }}</span>
                        </div>
                    </div>
                    @if($suggestions->has($line->id))
                    <div class="mt-2 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg px-3 py-2 text-xs text-blue-700 dark:text-blue-300">
                        ✦ Auto-suggestion available ({{ $suggestions->get($line->id)['tier'] }} match)
                    </div>
                    @endif
                </div>
                @empty
                <div class="px-5 py-12 text-center text-gray-400 dark:text-gray-500 text-sm">No statement lines found.</div>
                @endforelse
            </div>
            <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-700 text-xs text-gray-400">
                {{ $lines->links() }}
            </div>
        </div>

        {{-- Right: Confirm Match Form --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="font-bold text-gray-900 dark:text-white text-sm">Confirm Reconciliation</h2>
            </div>
            <div class="p-5" x-show="!selectedLine">
                <div class="flex flex-col items-center justify-center py-16 text-gray-400 dark:text-gray-500">
                    <svg class="w-10 h-10 mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <p class="text-sm">Select a statement line to begin matching</p>
                </div>
            </div>
            <form x-show="selectedLine" action="{{ route('bank-recon.confirm') }}" method="POST" class="p-5 space-y-4">
                @csrf
                <input type="hidden" name="statement_line_id" :value="selectedLine">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">GL Journal Entry ID</label>
                    <input type="number" name="journal_entry_id" placeholder="Enter Journal Entry ID..."
                           class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm" required>
                    <p class="text-xs text-gray-400 mt-1">Enter the corresponding journal entry from the GL.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nominal Difference / Bank Fee</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">$</span>
                        <input type="number" step="0.01" name="difference" x-model="difference" placeholder="0.00"
                               class="w-full pl-7 rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    </div>
                    <p x-show="difference > 0" class="text-xs text-amber-600 dark:text-amber-400 mt-1">
                        ⚠ An adjustment journal will be posted to the Bank Charges account automatically.
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes (optional)</label>
                    <textarea name="notes" rows="2" placeholder="Add reconciliation notes..."
                              class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm"></textarea>
                </div>
                <button type="submit"
                        class="w-full px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-bold transition-colors shadow-md shadow-emerald-200">
                    ✓ Confirm Reconciliation
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
