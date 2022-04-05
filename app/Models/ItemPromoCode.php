<?php

namespace App\Models;

class ItemPromoCode extends Common
{
    protected $guarded = ['id'];

    protected $table = 'item_promo_code';

    public $timestamps = false;

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function promoCode()
    {
        return $this->belongsTo(PromoCode::class);
    }

    public function consumedBy()
    {
        return $this->belongsTo(Customer::class);
    }
}

