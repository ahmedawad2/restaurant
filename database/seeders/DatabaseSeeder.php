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
    }
}
