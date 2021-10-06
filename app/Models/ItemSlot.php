<?php

namespace App\Models;


class ItemSlot extends Common
{
    protected $guarded = ['id'];
    public $timestamps = false;

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
