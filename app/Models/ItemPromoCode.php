<?php

namespace App\Models;

class ItemPromoCode extends Common
{
    public $timestamps = false;
    protected $guarded = ['id'];


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

