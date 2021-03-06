<?php


namespace App\Infra\Repositories;

use App\Infra\Interfaces\Repositories\OrdersRepositoryInterface;
use App\Models\Order;

class OrdersRepository implements OrdersRepositoryInterface
{

    private $model;

    public function __construct()
    {
        $this->model = new Order();
    }

    public function create(array $data = [])
    {
        $data['id'] = $this->model::generateUnique();
        try {
            return $this->model->create($data);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function readById($id): OrdersRepositoryInterface
    {
        $this->model = $this->model->where('id', $id);
        return $this;
    }

    public function select(array $columns): OrdersRepositoryInterface
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

    public function where($key, $value, $operator = '='): OrdersRepositoryInterface
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

    public function withReservation(array $selectColumns = []): OrdersRepositoryInterface
    {
        $this->model = $this->model->with('reservation', function ($q) use ($selectColumns) {
            if ($selectColumns) {
                $q->select(array_merge($selectColumns, ['id']));
            }
        });
        return $this;
    }

    public function withItems(array $selectColumns = []): OrdersRepositoryInterface
    {
        $this->model = $this->model->with('itemsPromoCodes', function ($q) use ($selectColumns) {
            if ($selectColumns) {
                $q->with('item', function ($q) use ($selectColumns) {
                    if ($selectColumns) {
                        $q->select(array_merge($selectColumns, ['id']));
                    }
                });
            }
        });
        return $this;
    }

    public function withPromoCodes(array $selectColumns = []): OrdersRepositoryInterface
    {
        $this->model = $this->model->with('itemsPromoCodes', function ($q) use ($selectColumns) {
            if ($selectColumns) {
                $q->with('promoCodes', function ($q) use ($selectColumns) {
                    if ($selectColumns) {
                        $q->select(array_merge($selectColumns, ['id']));
                    }
                });
            }
        });
        return $this;
    }

    public function count()
    {
        return $this->model->count();
    }
}
