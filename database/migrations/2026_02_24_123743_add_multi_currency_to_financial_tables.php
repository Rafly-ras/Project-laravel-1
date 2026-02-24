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
        $tables = ['sales_orders', 'invoices', 'payments', 'expenses', 'request_orders'];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                $table->foreignId('currency_id')->nullable()->constrained('currencies');
                $table->decimal('exchange_rate', 15, 6)->default(1);
                $table->decimal('base_amount', 15, 2)->nullable();

                if ($tableName === 'sales_orders') {
                    $table->decimal('base_gross_profit', 15, 2)->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        $tables = ['sales_orders', 'invoices', 'payments', 'expenses', 'request_orders'];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                $table->dropForeign(['currency_id']);
                
                $columns = ['currency_id', 'exchange_rate', 'base_amount'];
                if ($tableName === 'sales_orders') {
                    $columns[] = 'base_gross_profit';
                }
                
                $table->dropColumn($columns);
            });
        }
        Schema::enableForeignKeyConstraints();
    }
};
