<?php

namespace App\Console\Commands;

use App\Models\POS\POSProducts;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ImportOpenFoodFactsProducts extends Command
{
    protected $signature = 'pos:import-products';

    protected $description = 'Import products from Open Food Facts';

    public function handle(): int
    {
        $count = (int) $this->ask(
            'How many products would you like to import?',
            100
        );

        $tenantId = (int) $this->ask(
            'Tenant ID',
            1
        );

        $createdBy = (int) $this->ask(
            'Created By User ID',
            1
        );

        $pageSize = 100;
        $pages = (int) ceil($count / $pageSize);

        $imported = 0;

        $progressBar = $this->output->createProgressBar($count);

        $progressBar->start();

        for ($page = 1; $page <= $pages; $page++) {

            $response = Http::timeout(60)
                ->retry(3, 1000)
                ->get(
                    'https://world.openfoodfacts.org/api/v2/search',
                    [
                        'countries_tags_en' => 'philippines',
                        'page'              => $page,
                        'page_size'         => $pageSize,
                        'fields'            => implode(',', [
                            'code',
                            'product_name',
                            'brands',
                            'categories',
                        ]),
                    ]
                );

            if (!$response->successful()) {
                $this->newLine();
                $this->error(
                    "Failed to fetch page {$page}"
                );

                continue;
            }

            $products = $response->json(
                'products',
                []
            );

            if (empty($products)) {
                break;
            }

            foreach ($products as $product) {

                if ($imported >= $count) {
                    break 2;
                }

                $name = trim(
                    $product['product_name'] ?? ''
                );

                if (!$name) {
                    continue;
                }

                $barcode = trim(
                    $product['code'] ?? ''
                );

                if (
                    $barcode &&
                    POSProducts::where(
                        'barcode',
                        $barcode
                    )->exists()
                ) {
                    continue;
                }

                $costPrice = rand(10, 500);

                POSProducts::create([
                    'tenant_id'       => $tenantId,
                    'category_id'     => 1,
                    'unit_id'         => 1,
                    'barcode'         => $barcode ?: fake()->ean13(),
                    'sku'             => 'SKU-' . strtoupper(
                            Str::random(8)
                        ),
                    'name'            => Str::limit(
                        $name,
                        255,
                        ''
                    ),
                    'description'     => trim(
                        ($product['brands'] ?? '') .
                        (
                        !empty($product['categories'])
                            ? ' | ' . $product['categories']
                            : ''
                        )
                    ),
                    'cost_price'      => $costPrice,
                    'selling_price'   => round(
                        $costPrice * 1.25,
                        2
                    ),
                    'wholesale_price' => round(
                        $costPrice * 1.15,
                        2
                    ),
                    'reorder_level'   => rand(
                        5,
                        20
                    ),
                    'status'          => 'active',
                    'archived'        => false,
                    'created_by'      => $createdBy,
                    'updated_by'      => $createdBy,
                ]);

                $imported++;

                $progressBar->advance();
            }
        }

        $progressBar->finish();

        $this->newLine(2);

        $this->info(
            "Successfully imported {$imported} products."
        );

        return self::SUCCESS;
    }
}
