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
        Schema::table('journal_entries', function (Blueprint $table) {
            $table->string('hash', 64)->nullable()->unique()->after('accounting_period_id');
            $table->string('previous_hash', 64)->nullable()->after('hash');
            $table->timestamp('reversed_at')->nullable()->after('previous_hash');
            $table->foreignId('reversing_entry_id')->nullable()->constrained('journal_entries')->nullOnDelete()->after('reversed_at');
            $table->boolean('is_reversal')->default(false)->after('reversing_entry_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('journal_entries', function (Blueprint $table) {
            $table->dropForeign(['reversing_entry_id']);
            $table->dropColumn(['hash', 'previous_hash', 'reversed_at', 'reversing_entry_id', 'is_reversal']);
        });
    }
};
