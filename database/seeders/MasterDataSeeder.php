<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $categoryId = DB::table('categories')->insertGetId([
            'name' => 'Tops',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $sizes = [];
        foreach ([['S', 'S'], ['M', 'M'], ['L', 'L']] as [$name, $code]) {
            $sizes[] = [
                'id' => DB::table('sizes')->insertGetId([
                'name' => $name,
                'code' => $code,
                'created_at' => $now,
                'updated_at' => $now,
                ]),
                'code' => $code,
            ];
        }

        $colors = [];
        foreach ([['Black', 'BLK'], ['White', 'WHT']] as [$name, $code]) {
            $colors[] = [
                'id' => DB::table('colors')->insertGetId([
                'name' => $name,
                'code' => $code,
                'created_at' => $now,
                'updated_at' => $now,
                ]),
                'code' => $code,
            ];
        }

        $productId = DB::table('products')->insertGetId([
            'category_id' => $categoryId,
            'name' => 'Basic Tee',
            'description' => 'Cotton t-shirt for daily wear.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        foreach ($sizes as $size) {
            foreach ($colors as $color) {
                $sku = 'TEE-' . $size['code'] . '-' . $color['code'];
                $variantId = DB::table('product_variants')->insertGetId([
                    'product_id' => $productId,
                    'size_id' => $size['id'],
                    'color_id' => $color['id'],
                    'sku' => $sku,
                    'price' => 99000,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                DB::table('stocks')->insert([
                    'variant_id' => $variantId,
                    'qty' => 10,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        for ($i = 1; $i <= 20; $i++) {
            $productName = 'Product ' . str_pad((string) $i, 2, '0', STR_PAD_LEFT);
            $productCode = 'PRD' . str_pad((string) $i, 2, '0', STR_PAD_LEFT);
            $productId = DB::table('products')->insertGetId([
                'category_id' => $categoryId,
                'name' => $productName,
                'description' => 'Auto generated item.',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            foreach ($sizes as $size) {
                foreach ($colors as $color) {
                    $sku = $productCode . '-' . $size['code'] . '-' . $color['code'];
                    $variantId = DB::table('product_variants')->insertGetId([
                        'product_id' => $productId,
                        'size_id' => $size['id'],
                        'color_id' => $color['id'],
                        'sku' => $sku,
                        'price' => 99000 + ($i * 1000),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

                    DB::table('stocks')->insert([
                        'variant_id' => $variantId,
                        'qty' => 8 + $i,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }
    }
}
