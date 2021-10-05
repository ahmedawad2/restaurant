<?php

namespace Database\Seeders;

use App\Models\Table;
use Illuminate\Database\Seeder;

class TablesSeeder extends Seeder
{

    public function run()
    {
        Table::truncate();
        for ($i = 0; $i < 50; $i++) {
            $data[] = [
                'capacity' => rand(2, 10)
            ];
        }
        Table::insert($data);
    }
}
