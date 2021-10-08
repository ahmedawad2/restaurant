<?php

namespace App\Models;


use Carbon\Carbon;

class Item extends Common
{
    protected $guarded = ['id'];
    public $timestamps = false;

    public function slots()
    {
        return $this->hasMany(ItemSlot::class);
    }

    public function promoCodes()
    {
        return $this->belongsToMany(PromoCode::class);
    }

    public function validPromoCodes()
    {
        return $this->belongsToMany(PromoCode::class)
            ->where('consumed_by', null)
            ->where('from', '<', Carbon::now())
            ->where('to', '>', Carbon::now())
            ->withPivot(['id']);
    }
}
