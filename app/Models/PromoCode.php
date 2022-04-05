<?php

namespace App\Models;


class PromoCode extends Common
{
    protected $guarded = ['id'];

    protected $dates = ['from', 'to'];

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $timestamps = false;

    public $incrementing = false;
}

