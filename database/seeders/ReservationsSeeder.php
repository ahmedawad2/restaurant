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

        for ($j = 0; $j < 1; $j++) {
            $data = [];
            for ($i = 0; $i < 10000; $i++) {
                shuffle($reservationStatuses);
                $from = Carbon::now()->addDays(rand(-3, 5))
                    ->addHours(rand(0, 23))
                    ->addMinutes(rand(0, 59))
                    ->addSeconds(rand(0, 59));
                $data[] = [
                    'customer_id' => rand(1, $customersCount),
                    'table_id' => rand(1, $tablesCount),
                    'from' => $from,
                    'to' => (clone $from)->addMinutes(rand(30, 120)),
                    'status' => $reservationStatuses[0],
                ];
            }
            Reservation::insert($data);
        }
    }
}
