<?php


namespace App\Infra\Repositories;

use App\Infra\Interfaces\Repositories\ReservationsRepositoryInterface;
use App\Models\Reservation;

class ReservationsRepository implements ReservationsRepositoryInterface
{

    private $model;

    public function __construct()
    {
        $this->model = new Reservation();
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

    public function select(array $columns): ReservationsRepositoryInterface
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
}
