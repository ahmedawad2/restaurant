<?php

namespace Database\Seeders;

use App\Models\Item;
use Faker\Factory;
use Illuminate\Database\Seeder;

class ItemsSeeder extends Seeder
{
    public function run()
    {
        $faker = Factory::create();

        Item::truncate();

        for ($i = 0; $i < 100; $i++) {
            $data[] = [
                'name' => $faker->firstName,
                'price' => rand(100, 300) * rand(1, 9) / 10,
                'qty' => rand(20, 60) * rand(1, 9) / 10
            ];
        }

        Item::insert($data);
    }
}
