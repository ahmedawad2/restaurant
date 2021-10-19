<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\ItemPromoCode;
use App\Models\PromoCode;
use Illuminate\Database\Seeder;

class ItemPromoCodesSeeder extends Seeder
{
    public function run()
    {
        ItemPromoCode::truncate();

        foreach (PromoCode::get(['id']) as $promoCode) {
                $onItems = Item::inRandomOrder()
                    ->limit(rand(5, 10))
                    ->get(['id'])->pluck('id')->toArray();
                foreach ($onItems as $itemId) {
                    $data[] = [
                        'promo_code_id' => $promoCode->id,
                        'item_id' => $itemId,
                    ];
                }
        }

        ItemPromoCode::insert($data);
    }
}
