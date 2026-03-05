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
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->foreignId('manager_id')->nullable()->constrained('users');
            $table->timestamps();
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->decimal('credit_limit', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained();
            $table->foreignId('account_id')->constrained();
            $table->foreignId('accounting_period_id')->constrained();
            $table->decimal('amount_limit', 15, 2);
            $table->decimal('spent_amount', 15, 2)->default(0);
            $table->timestamps();

            $table->unique(['department_id', 'account_id', 'accounting_period_id']);
        });

        Schema::create('approval_matrices', function (Blueprint $table) {
            $table->id();
            $table->string('document_type'); // e.g., 'purchase_request', 'expense'
            $table->decimal('min_amount', 15, 2);
            $table->foreignId('role_id')->constrained();
            $table->integer('sequence')->default(1);
            $table->timestamps();
        });

        Schema::create('approvals', function (Blueprint $table) {
            $table->id();
            $table->morphs('approvable');
            $table->foreignId('user_id')->constrained();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approvals');
        Schema::dropIfExists('approval_matrices');
        Schema::dropIfExists('budgets');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('departments');
    }
};
