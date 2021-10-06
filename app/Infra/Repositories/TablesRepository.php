<?php


namespace App\Infra\Repositories;

use App\Infra\Interfaces\Repositories\TablesRepositoryInterface;
use App\Models\Table;

class TablesRepository implements TablesRepositoryInterface
{

    private $model;

    public function __construct()
    {
        $this->model = new Table();
    }

    public function create(array $data = [])
    {
        try {
            return $this->model->create($data);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function readById(int $id)
    {
        $this->model = $this->model->where('id', $id);
        return $this;
    }

    public function activeCustomers(): TablesRepositoryInterface
    {
        $this->model = $this->model->where('status', true);
        return $this;
    }

    public function inActiveCustomers(): TablesRepositoryInterface
    {
        $this->model = $this->model->where('status', false);
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
//                ->where('capacity', '>=', $capacity)
//                ->orderBy('capacity');
//        return $this;
//    }

    public function toBeReserved(string $from, string $to, int $capacity): ?array
    {
        $table =
            $this->model->whereRaw("id not in (select distinct table_id
                 from reservations
                 where reservations.from >= '"
                . $from . "'
                   and reservations.to <= '"
                . $to . "'
                   and reservations.status in (1, 2)) and capacity >= "
                . $capacity . " order by capacity asc limit 1")->get();
        return count($table)
            ? $table[0]->toArray()
            : null;
    }
}
