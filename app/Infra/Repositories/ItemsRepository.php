<?php


namespace App\Infra\Repositories;

use App\Infra\Interfaces\Repositories\ItemsRepositoryInterface;
use App\Models\Item;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ItemsRepository implements ItemsRepositoryInterface
{

    private $model;
    private string $table;

    public function __construct()
    {
        $this->model = new Item();
        $this->table = $this->model->getTable();
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

    public function withValidPromoCodes(array $selectColumns = [], array $inPromoCodes = []): ItemsRepositoryInterface
    {
        $this->select(['id']);
        $this->model = $this->model->with(['validPromoCodes' => function ($q) use ($selectColumns, $inPromoCodes) {
            if ($inPromoCodes) {
                $q->whereIn('promo_codes.id', $inPromoCodes);
            }
            if ($selectColumns) {
                $q->select(array_map(function ($column) {
                    return 'promo_codes.' . $column;
                }, $selectColumns));
            }
        }]);
        return $this;
    }

    public function whereIn(string $column, array $values): ItemsRepositoryInterface
    {
        $this->model = $this->model->whereIn($column, $values);
        return $this;
    }

    public function decrement(string $column, $value)
    {
        $this->model->decrement($column, $value);
    }

    public function decreaseQty(array $idsWithQty): ItemsRepositoryInterface
    {
        $query = 'update ' . $this->table . ' set qty = case id ';
        foreach ($idsWithQty as $itemId => $qty) {
            $query .= ' when ' . $itemId . ' then qty-' . $qty;
        }
        $query .= ' end ';
        $query .= 'where id in(';
        $query .= implode(',', array_keys($idsWithQty));
        $query .= ')';
        DB::statement($query);
        return $this->reset();
    }
}
