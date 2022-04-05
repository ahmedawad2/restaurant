<?php

namespace App\Models;


class Reservation extends Common
{
    protected $dates = ['created_at', 'from', 'to'];

    protected $guarded = ['id'];

    public $timestamps = false;

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }
}
