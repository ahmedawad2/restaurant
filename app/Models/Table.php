<?php

namespace App\Models;


class Table extends Common
{
    public $timestamps = false;

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
