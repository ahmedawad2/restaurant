<?php

namespace App\Models;


class PromoCode extends Common
{
    protected $guarded = ['id'];
    protected $dates = ['from', 'to'];
    public $timestamps = false;
}

