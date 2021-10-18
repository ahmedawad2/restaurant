<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call(CustomersSeeder::class);
        $this->call(TablesSeeder::class);
        $this->call(ReservationsSeeder::class);
        $this->call(ItemsSeeder::class);
        $this->call(ItemsSlotsSeeder::class);
        $this->call(PromoCodesSeeder::class);
        $this->call(ItemPromoCodesSeeder::class);
    }
}
