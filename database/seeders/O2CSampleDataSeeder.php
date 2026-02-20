<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\RequestOrder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class O2CSampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first();
        $products = Product::take(3)->get();
        $warehouse = Warehouse::first();

        if ($products->count() < 1) return;

        DB::beginTransaction();
        try {
            // 1. Create a Draft RO
            $ro1 = RequestOrder::create([
                'customer_name' => 'Tech Corp',
                'customer_email' => 'contact@techcorp.com',
                'total_amount' => $products[0]->price * 5,
                'created_by' => $admin->id,
                'status' => 'draft',
            ]);
            $ro1->items()->create([
                'product_id' => $products[0]->id,
                'qty' => 5,
                'price' => $products[0]->price,
                'subtotal' => $products[0]->price * 5,
            ]);

            // 2. Create an Approved RO
            $ro2 = RequestOrder::create([
                'customer_name' => 'Future Inc',
                'customer_email' => 'sales@future.io',
                'total_amount' => $products[1]->price * 10,
                'created_by' => $admin->id,
                'status' => 'approved',
                'approved_by' => $admin->id,
                'approved_at' => now(),
            ]);
            $ro2->items()->create([
                'product_id' => $products[1]->id,
                'qty' => 10,
                'price' => $products[1]->price,
                'subtotal' => $products[1]->price * 10,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
