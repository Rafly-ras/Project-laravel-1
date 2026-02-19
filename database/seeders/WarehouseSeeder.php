<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $warehouse = \App\Models\Warehouse::firstOrCreate([
            'name' => 'Main Warehouse',
        ], [
            'location' => 'Headquarters',
            'is_active' => true,
        ]);

        // Link existing products to this warehouse
        $products = \App\Models\Product::all();
        foreach ($products as $product) {
            $product->warehouses()->syncWithoutDetaching([
                $warehouse->id => ['stock' => $product->stock]
            ]);
        }

        // Link existing transactions to this warehouse
        \App\Models\ProductTransaction::whereNull('warehouse_id')->update([
            'warehouse_id' => $warehouse->id
        ]);
    }
}
