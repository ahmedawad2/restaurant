<?php

namespace App\Models;

use Staudenmeir\EloquentJsonRelations\HasJsonRelationships;

class ItemOrderPromoCode extends Common
{
    use HasJsonRelationships;

    protected $guarded = ['id'];
    protected $table = 'item_order_promo_code';
    protected $casts = [
        'promo_codes' => 'json',
    ];
    public $timestamps = false;

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function promoCodes()
    {
        return $this->belongsToJson(PromoCode::class, 'promo_codes');
    }
}
