<?php

namespace App\Models;

class ItemOrder extends Common
{
    protected $guarded = ['id'];

    public $timestamps = false;

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
