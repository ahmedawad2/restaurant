<?php


namespace App\Infra\Repositories\Common;

use App\Infra\Interfaces\Repositories\Common\CustomerRepositoryInterface;
use App\Models\Customer;

class CustomerRepository implements CustomerRepositoryInterface
{

    private $model;

    public function __construct()
    {
        $this->model = new Customer();
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

    public function activeCustomers(): CustomerRepositoryInterface
    {
        $this->model = $this->model->where('status', true);
        return $this;
    }

    public function inActiveCustomers(): CustomerRepositoryInterface
    {
        $this->model = $this->model->where('status', false);
        return $this;
    }

    public function select(array $columns): CustomerRepositoryInterface
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
            dd($e->getMessage());
            return false;
        }
    }
}
