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
        // Request Orders
        Schema::create('request_orders', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique();
            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->enum('status', ['draft', 'approved', 'rejected', 'converted'])->default('draft');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('request_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained();
            $table->integer('qty');
            $table->decimal('price', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });

        // Sales Orders
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->id();
            $table->string('sales_number')->unique();
            $table->foreignId('request_order_id')->nullable()->constrained();
            $table->string('customer_name');
            $table->enum('status', ['draft', 'confirmed', 'delivered', 'invoiced', 'cancelled'])->default('draft');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('sales_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained();
            $table->foreignId('warehouse_id')->constrained();
            $table->integer('qty');
            $table->decimal('price', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });

        // Invoices
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('sales_order_id')->constrained();
            $table->date('due_date');
            $table->decimal('total_amount', 15, 2);
            $table->enum('status', ['unpaid', 'partial', 'paid', 'overdue'])->default('unpaid');
            $table->timestamp('issued_at')->nullable();
            $table->timestamps();
        });

        // Payments
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained();
            $table->string('payment_number')->unique();
            $table->decimal('amount', 15, 2);
            $table->string('payment_method'); // cash, transfer, e-wallet
            $table->string('reference_number')->nullable();
            $table->timestamp('paid_at');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('sales_order_items');
        Schema::dropIfExists('sales_orders');
        Schema::dropIfExists('request_order_items');
        Schema::dropIfExists('request_orders');
    }
};
