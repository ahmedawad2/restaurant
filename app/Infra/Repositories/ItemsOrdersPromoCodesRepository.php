<?php


namespace App\Infra\Repositories;

use App\Infra\Interfaces\Repositories\ItemsOrdersPromoCodesRepositoryInterface;
use App\Models\ItemOrderPromoCode;

class ItemsOrdersPromoCodesRepository implements ItemsOrdersPromoCodesRepositoryInterface
{

    private $model;

    public function __construct()
    {
        $this->model = new ItemOrderPromoCode();
    }

    public function create(array $data = [])
    {
        try {
            return $this->model->create($data);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function readById(int $id): ItemsOrdersPromoCodesRepositoryInterface
    {
        $this->model = $this->model->where('id', $id);
        return $this;
    }

    public function select(array $columns): ItemsOrdersPromoCodesRepositoryInterface
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

    public function where($key, $value, $operator = '='): ItemsOrdersPromoCodesRepositoryInterface
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
    public function insert(array $data = []): bool
    {
        try {
            return (bool)$this->model->insert($data);
        } catch (\Exception $e) {
            dd($e->getMessage());
            return false;
        }
    }
}
