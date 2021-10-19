<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\ItemSlot;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Database\Seeder;

class ItemsSlotsSeeder extends Seeder
{
    public function run()
    {
        $weekDays = range(0, 6);

        ItemSlot::truncate();

        for ($i = 1; $i <= Item::count(); $i++) {
            $servingDays = rand(2, 7);
            for ($j = 0; $j < $servingDays; $j++) {
                $data[] = [
                    'item_id' => $i,
                    'day' => $weekDays[$j],
                    'from' => rand(0, 12) . ':' . rand(0, 59) . ':' . rand(0, 59),
                    'to' => rand(13, 23) . ':' . rand(0, 59) . ':' . rand(0, 59),
                ];
            }
        }

        ItemSlot::insert(array_values($data));
    }
}
