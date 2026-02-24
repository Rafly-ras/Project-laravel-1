<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usd = \App\Models\Currency::create([
            'code' => 'USD',
            'name' => 'United States Dollar',
            'symbol' => '$',
            'is_base' => true,
        ]);

        $idr = \App\Models\Currency::create([
            'code' => 'IDR',
            'name' => 'Indonesian Rupiah',
            'symbol' => 'Rp',
            'is_base' => false,
        ]);

        $eur = \App\Models\Currency::create([
            'code' => 'EUR',
            'name' => 'Euro',
            'symbol' => '€',
            'is_base' => false,
        ]);

        // Initial Exchange Rates (Base: USD)
        \App\Models\ExchangeRate::create([
            'from_currency_id' => $idr->id,
            'to_currency_id' => $usd->id,
            'rate' => 0.000064, // 1 IDR = 0.000064 USD
            'rate_date' => now()->toDateString(),
        ]);

        \App\Models\ExchangeRate::create([
            'from_currency_id' => $eur->id,
            'to_currency_id' => $usd->id,
            'rate' => 1.08, // 1 EUR = 1.08 USD
            'rate_date' => now()->toDateString(),
        ]);
        
        \App\Models\ExchangeRate::create([
            'from_currency_id' => $usd->id,
            'to_currency_id' => $usd->id,
            'rate' => 1,
            'rate_date' => now()->toDateString(),
        ]);
    }
}
