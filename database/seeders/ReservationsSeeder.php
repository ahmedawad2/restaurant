<?php

namespace Database\Seeders;

use App\Infra\Classes\BusinessLogic\Reservations\ReservationStatuses;
use App\Models\Customer;
use App\Models\Reservation;
use App\Models\Table;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ReservationsSeeder extends Seeder
{
    public function run()
    {
        Reservation::truncate();

        $customersCount = Customer::count();
        $tablesCount = Table::count();
        $reservationStatuses = ReservationStatuses::allStatuses();

        for ($i = 0; $i < 1000; $i++) {
            shuffle($reservationStatuses);
            $data[] = [
                'customer_id' => rand(1, $customersCount),
                'table_id' => rand(1, $tablesCount),
                'from' => Carbon::now()->addDay(rand(-3, 5))
                    ->addHour(rand(0, 23))
                    ->addMinute(rand(0, 59))
                    ->addSecond(0, 59),
                'status' => $reservationStatuses[0],
            ];
        }

        Reservation::insert($data);
    }
}
