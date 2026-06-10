<?php

namespace App\Actions\POS\Products;

use App\Models\POS\POSProducts;
use App\Models\POS\ProductPriceHistory;
use App\Models\User;
use App\Traits\TCommonFunctions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class StoreProduct
{
    use TCommonFunctions;
    public function handle(
        array $data,
        User $user,
        ?UploadedFile $image = null
    ): POSProducts
    {
        return DB::transaction(function () use ($data, $user, $image) {

            $newProduct = new POSProducts();

            $newProduct->tenant_id = $user->tenant_id;
            $newProduct->category_id = $data['category_id'] ?? null;
            $newProduct->unit_id = $data['unit_id'] ?? null;
            $newProduct->barcode = $data['barcode'] ?? null;
            $newProduct->sku = $data['sku'] ?? null;
            $newProduct->name = $data['name'];
            $newProduct->description = $data['description'] ?? null;
            $newProduct->cost_price = $data['cost_price'];
            $newProduct->selling_price = $data['selling_price'];
            $newProduct->wholesale_price = $data['wholesale_price'] ?? 0;
            $newProduct->reorder_level = $data['reorder_level'] ?? 0;

            if ($image) {
                $newProduct->image = $image->store(
                    'products',
                    'public'
                );
            }

            $this->setCommonFields($newProduct, $data);
            $newProduct->save();


            $priceHistory = new ProductPriceHistory();
            $priceHistory->tenant_id = $user->tenant_id;
            $priceHistory->product_id = $newProduct->id;
            $priceHistory->cost_price = $newProduct->cost_price;
            $priceHistory->selling_price = $newProduct->selling_price;
            $priceHistory->wholesale_price = $newProduct->wholesale_price;
            $priceHistory->remarks = 'Initial product price';
            $priceHistory->effective_date = now();
            $this->setCommonFields(
                $priceHistory,
                $data
            );
            $priceHistory->save();


            return $newProduct;
        });
    }
}
