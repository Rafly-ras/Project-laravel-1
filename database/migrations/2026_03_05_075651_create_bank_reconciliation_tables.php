<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bank Statement batches (CSV imports)
        Schema::create('bank_statements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_account_id')->constrained('accounts');
            $table->foreignId('imported_by')->constrained('users');
            $table->string('filename');
            $table->date('statement_date');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->timestamps();
        });

        // Individual lines from the bank statement CSV
        Schema::create('bank_statement_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_statement_id')->constrained()->onDelete('cascade');
            $table->date('transaction_date');
            $table->string('description');
            $table->string('reference')->nullable();
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            $table->decimal('running_balance', 15, 2)->default(0);
            $table->enum('status', ['unmatched', 'suggested', 'reconciled'])->default('unmatched');
            $table->timestamps();
        });

        // Reconciliation records: 1 statement line ↔ 1..N journal entries
        Schema::create('bank_reconciliations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('statement_line_id')->constrained('bank_statement_lines');
            $table->foreignId('journal_entry_id')->constrained();
            $table->foreignId('reconciled_by')->constrained('users');
            $table->enum('match_type', ['exact', 'fuzzy', 'manual', 'split'])->default('manual');
            $table->decimal('amount_matched', 15, 2);
            $table->decimal('difference', 15, 2)->default(0);
            $table->foreignId('adjustment_journal_id')->nullable()->constrained('journal_entries');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_reconciliations');
        Schema::dropIfExists('bank_statement_lines');
        Schema::dropIfExists('bank_statements');
    }
};
