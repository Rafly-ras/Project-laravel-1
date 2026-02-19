<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Electronics',
            'Furniture',
            'Clothing',
            'Food & Beverage',
            'Books',
            'Toys',
            'Uncategorized',
        ];

        foreach ($categories as $category) {
            \App\Models\Category::firstOrCreate(['name' => $category]);
        }

        // Assign 'Uncategorized' to existing products with no category
        $uncategorized = \App\Models\Category::where('name', 'Uncategorized')->first();
        if ($uncategorized) {
            \App\Models\Product::whereNull('category_id')->update(['category_id' => $uncategorized->id]);
        }

        // Seed sample products
        $electronics = \App\Models\Category::where('name', 'Electronics')->first();
        if ($electronics) {
            \App\Models\Product::firstOrCreate(
                ['name' => 'Smartphone X'],
                ['price' => 999.99, 'stock' => 50, 'category_id' => $electronics->id]
            );
            \App\Models\Product::firstOrCreate(
                ['name' => 'Laptop Pro'],
                ['price' => 1499.99, 'stock' => 20, 'category_id' => $electronics->id]
            );
        }

        $furniture = \App\Models\Category::where('name', 'Furniture')->first();
        if ($furniture) {
            \App\Models\Product::firstOrCreate(
                ['name' => 'Ergonomic Chair'],
                ['price' => 299.99, 'stock' => 15, 'category_id' => $furniture->id]
            );
        }
    }
}
