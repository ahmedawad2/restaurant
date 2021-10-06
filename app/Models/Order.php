<?php

namespace App\Models;


class Order extends Common
{
    protected $guarded = ['id'];
    protected $dates = ['created_at'];
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $timestamps = false;
    public $incrementing = false;
}

