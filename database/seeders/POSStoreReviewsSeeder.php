<?php

namespace Database\Seeders;

use App\Models\POS\POSStoreReview;
use App\Models\POS\POSTenant;
use Illuminate\Database\Seeder;

class POSStoreReviewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenant1 = POSTenant::first();
        $tenantId = $tenant1 ? $tenant1->id : null;

        $reviews = [
            [
                'tenant_id'       => $tenantId,
                'reviewer_name'   => 'Aling Maria Santos',
                'store_name'      => 'San Jose Minimart',
                'avatar_initials' => 'MS',
                'rating'          => 5,
                'review_text'     => 'Napakalaking tulong sa aming Minimart! Dati laging kulang ang benta sa hapon, ngayon kitang-kita sa audit log kung anong nangyari per shift.',
                'is_approved'     => 1,
                'is_featured'     => 1,
            ],
            [
                'tenant_id'       => $tenantId,
                'reviewer_name'   => 'Mang Robert Cruz',
                'store_name'      => 'R&M Grocery Store',
                'avatar_initials' => 'RC',
                'rating'          => 5,
                'review_text'     => 'Yung Utang CRM feature ang pinaka-favorite ko. Dati nawawala ang cuaderno sa utang, ngayon mabilis na mag-remind sa mga suki!',
                'is_approved'     => 1,
                'is_featured'     => 1,
            ],
            [
                'tenant_id'       => $tenantId,
                'reviewer_name'   => 'Chef Liza Tan',
                'store_name'      => 'Liza Retail Mart Chain',
                'avatar_initials' => 'LT',
                'rating'          => 5,
                'review_text'     => 'Subok na subok sa 3 branches namin! Madaling i-monitor ang inventory kahit nasa bahay lang ako gamit ang cellphone.',
                'is_approved'     => 1,
                'is_featured'     => 1,
            ],
            [
                'tenant_id'       => $tenantId,
                'reviewer_name'   => 'Kuya Cardo Dalisay',
                'store_name'      => 'Cardo Express Minimart',
                'avatar_initials' => 'CD',
                'rating'          => 5,
                'review_text'     => 'Super bilis ng barcode scanning sa POS terminal. Hindi na nag-aantay nang matagal ang mga mamimili tuwing rush hour!',
                'is_approved'     => 1,
                'is_featured'     => 1,
            ],
        ];

        foreach ($reviews as $data) {
            POSStoreReview::updateOrCreate(
                ['reviewer_name' => $data['reviewer_name'], 'store_name' => $data['store_name']],
                $data
            );
        }
    }
}
