<?php

namespace Database\Seeders;

use App\Infra\Classes\Common\SerialGenerator;
use App\Models\PromoCode;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PromoCodesSeeder extends Seeder
{
    public function run()
    {
        PromoCode::truncate();

        for ($i = 0; $i < 50; $i++) {
            $data[] = [
                'id' => SerialGenerator::alphaNum(5),
                'reward' => rand(20, 50),
                'type' => rand(1, 2),
                'from' => Carbon::now()
                    ->addDays(rand(-6, 1))
                    ->addHours(rand(0, 11))
                    ->addMinutes(rand(0, 59)),
                'to' => Carbon::now()
                    ->addDays(rand(3, 5))
                    ->addHours(rand(0, 11))
                    ->addMinutes(rand(0, 59)),
            ];
        }

        PromoCode::insert($data);
    }
}
