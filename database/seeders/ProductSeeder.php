<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    private const TARGET = 20;

    /**
     * Tops the table up to TARGET products, keeping existing rows.
     */
    public function run(): void
    {
        $missing = self::TARGET - Product::count();

        if ($missing > 0) {
            Product::factory($missing)->create();
        }
    }
}
