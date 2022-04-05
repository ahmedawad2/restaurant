<?php

namespace App\Models;


use App\Infra\Traits\KeyGenerationTrait;

class Order extends Common
{
    use KeyGenerationTrait;

    protected static string $keyColumnName = 'id';

    protected static int $keyLength = 10;

    protected static string $keyGenerationType = 'alphanumeric';

    protected $guarded = [];

    protected $dates = ['created_at'];

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $timestamps = false;

    public $incrementing = false;

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function itemsPromoCodes()
    {
        return $this->hasMany(ItemOrderPromoCode::class);
    }
}

