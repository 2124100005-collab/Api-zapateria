<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductStockSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['product_id' => 1, 'stock' => 25],
            ['product_id' => 2, 'stock' => 40],
            ['product_id' => 3, 'stock' => 15],
            ['product_id' => 4, 'stock' => 10],
            ['product_id' => 5, 'stock' => 20],
            ['product_id' => 6, 'stock' => 35],
        ];

        foreach ($products as $product) {
            DB::table('product_stock')->updateOrInsert(
                ['product_id' => $product['product_id']],
                ['stock' => $product['stock'], 'updated_at' => now(), 'created_at' => now()]
            );
        }
    }
}