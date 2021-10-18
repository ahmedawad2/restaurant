<?php


namespace App\Infra\Repositories;

use App\Infra\Classes\BusinessLogic\Reservations\ReservationStatuses;
use App\Infra\Interfaces\Repositories\TablesRepositoryInterface;
use App\Models\Table;
use Illuminate\Support\Facades\DB;

class TablesRepository implements TablesRepositoryInterface
{
    private $model;

    public function __construct()
    {
        $this->model = new Table();
    }

    private function prepareSearchQuery(string $from, string $to, int $capacity): string
    {
        return "id not in (select distinct table_id from reservations where reservations.from >= '"
            . $from
            . "' and reservations.to <= '"
            . $to
            . "' and reservations.status in (%s)) and capacity = "
            . $capacity
            . " order by capacity asc limit 1";
    }

    public function create(array $data = [])
    {
        try {
            return $this->model->create($data);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function readById($id)
    {
        $this->model = $this->model->where('id', $id);
        return $this;
    }

    public function select(array $columns): TablesRepositoryInterface
    {
        $this->model = $this->model->addSelect($columns);
        return $this;
    }

    public function get()
    {
        return $this->model->get();
    }

    public function first()
    {
        return $this->model->first();
    }

    public function where($key, $value, $operator = '=')
    {
        $this->model = $this->model->where($key, $operator, $value);
        return $this;
    }

    public function reset()
    {
        return new self();
    }

    public function update(array $data = [])
    {
        try {
            return $this->model->update($data);
        } catch (\Exception $e) {
            return false;
        }
    }


    //Laravel's version of the query
//    public function toBeReserved(string $from, string $to, int $capacity): TablesRepositoryInterface
//    {
//        $this->model =
//            $this->model->whereDoesntHave('reservations', function ($q) use ($from, $to, $capacity) {
//                $q
//                    ->where('from', '>=', $from)
//                    ->where('to', '<=', $to)
//                    ->whereIn('status', [1, 2]);
//            })
//                ->where('capacity', '=', $capacity)
//                ->orderBy('capacity');
//        return $this;
//    }

    public function forActiveReservation(string $from, string $to, int $capacity): ?array
    {
        $table =
            $this->model->whereRaw(
                sprintf(
                    $this->prepareSearchQuery($from, $to, $capacity),
                    ReservationStatuses::RESERVATION_ACTIVE . ',' . ReservationStatuses::RESERVATION_SETTLED
                )
            )->get();
        return count($table)
            ? $table[0]->toArray()
            : null;
    }

    public function forWaitingReservation(string $from, string $to, int $capacity): ?array
    {
        $table =
            $this->model->whereRaw(
                sprintf(
                    $this->prepareSearchQuery($from, $to, $capacity),
                    ReservationStatuses::RESERVATION_WAITING
                )
            )->get();
        return count($table)
            ? $table[0]->toArray()
            : null;
    }

    public function forOverWaitingReservation(string $from, string $to, int $capacity): int
    {
        $table = DB::select(
            "select table_id, count(table_id) as c from reservations where status = "
            . ReservationStatuses::RESERVATION_WAITING
            . " and reservations.from >= '"
            . $from
            . "' and reservations.to <= '"
            . $to
            . "' group by table_id order by c limit 1");
        return $table[0]->table_id;
    }
}
