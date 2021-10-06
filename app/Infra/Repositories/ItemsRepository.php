<?php


namespace App\Infra\Repositories;

use App\Infra\Interfaces\Repositories\ItemsRepositoryInterface;
use App\Models\Item;
use Carbon\Carbon;

class ItemsRepository implements ItemsRepositoryInterface
{

    private $model;

    public function __construct()
    {
        $this->model = new Item();
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


    public function select(array $columns): ItemsRepositoryInterface
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

    public function available(int $day = null, string $fromTime = null, $toTime = null): ItemsRepositoryInterface
    {
        $day = $day ?? Carbon::now()->dayOfWeek;
        $fromTime = $fromTime ?? Carbon::now()->format('H:i:s');
        $this->select(['id']);
        $this->model = $this->model
            ->where('qty', '>', 0)
            ->whereHas('slots', function ($q) use ($day, $fromTime, $toTime) {
                $q
                    ->where('day', $day)
                    ->where('from', '>=', $fromTime);
                if ($toTime) {
                    $q->where('to', '<=', $toTime);
                }
            });
        return $this;
    }
}
