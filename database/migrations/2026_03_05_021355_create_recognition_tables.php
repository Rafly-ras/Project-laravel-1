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
        Schema::create('recognition_schedules', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['Revenue', 'Expense']);
            $table->string('source_type');
            $table->unsignedBigInteger('source_id');
            $table->decimal('total_amount', 15, 2);
            $table->foreignId('currency_id')->constrained();
            $table->decimal('exchange_rate', 15, 6);
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('periods');
            $table->enum('status', ['Active', 'Completed', 'Terminated'])->default('Active');
            $table->timestamps();

            $table->index(['source_type', 'source_id']);
        });

        Schema::create('recognition_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recognition_schedule_id')->constrained()->onDelete('cascade');
            $table->date('scheduled_date');
            $table->decimal('amount', 15, 2);
            $table->decimal('base_amount', 15, 2);
            $table->foreignId('journal_entry_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('status', ['Pending', 'Posted', 'Cancelled'])->default('Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recognition_lines');
        Schema::dropIfExists('recognition_schedules');
    }
};
