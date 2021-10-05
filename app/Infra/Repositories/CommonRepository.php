<?php


namespace App\Infra\Repositories;

use App\Infra\Interfaces\Repositories\CommonRepositoryInterface;
use App\Infra\Models\Common;
use App\Infra\Traits\ModelTrait;

class CommonRepository implements CommonRepositoryInterface
{
    use ModelTrait;

    private $model;

    public function __construct(Common $model)
    {
        $this->model = $model;
        $this->setModel($model);
    }

    public function create(array $data = [])
    {
        return $this->getModel()->create($data);
    }

    public function readById($id)
    {
        $this->model = $this->model->where('id', $id);
        return $this;
    }

    public function readByUnique($key = null, $value = null)
    {
    }

    public function select(array $columns)
    {
//        $this->selectColumns = $columns;
        $this->model = $this->model->select($columns);
//        dd($this->model);
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
}
