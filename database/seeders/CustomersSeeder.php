<?php

namespace Database\Seeders;

use App\Infra\Classes\Common\SerialGenerator;
use App\Models\Customer;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CustomersSeeder extends Seeder
{
    public function run()
    {


        $faker = Factory::create();
        $phonePrefixes = ['11', '10', '12'];
        Customer::truncate();

        for ($i = 0; $i < 100; $i++) {
            shuffle($phonePrefixes);
            $mobile = $phonePrefixes[0] . SerialGenerator::numeric(8);
            $data[] = [
                'mobile' => $mobile,
                'name' => $faker->firstName,
                'password' => Hash::make($mobile),
                'status' => true
            ];
        }
        $uniqueData = [];
        foreach ($data as $row) {
            isset($uniqueData[$row['mobile']]) or $uniqueData[$row['mobile']] = $row;
        }
        Customer::insert(array_values($uniqueData));
    }
}
