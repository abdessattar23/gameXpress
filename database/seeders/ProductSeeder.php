<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'name' => 'Sample Product',
            'description' => 'This is a sample product description',
            'slug' => 'This-is-a-sample-product',
            'price' => 19.99,
            'category_id' => 1,
            'stock' => 10
        ]);
    }
}
