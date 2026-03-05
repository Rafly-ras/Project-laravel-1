<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('account_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained()->onDelete('cascade');
            $table->foreignId('accounting_period_id')->constrained()->onDelete('cascade');
            $table->decimal('ending_balance', 18, 2);
            $table->decimal('debit_turnover', 18, 2);
            $table->decimal('credit_turnover', 18, 2);
            $table->decimal('base_ending_balance', 18, 2);
            $table->decimal('base_debit_turnover', 18, 2);
            $table->decimal('base_credit_turnover', 18, 2);
            $table->timestamps();

            $table->unique(['account_id', 'accounting_period_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_snapshots');
    }
};
