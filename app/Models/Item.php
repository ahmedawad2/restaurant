<?php

namespace App\Models;


class Item extends Common
{
    protected $guarded = ['id'];
    public $timestamps = false;

    public function slots()
    {
        return $this->hasMany(ItemSlot::class);
    }
}
