<?php


namespace App\Infra\Repositories\Notifications;


use App\Infra\Classes\Common\Constants;
use App\Infra\Interfaces\Repositories\Notifications\PushNotificationInterface;
use App\Infra\Interfaces\Repositories\Notifications\PushTokenInterface;
use App\Infra\Models\Notifications\PushNotification;
use App\Infra\Models\Notifications\PushToken;
use App\Infra\Traits\CastTranslationColumnTrait;

class PushNotificationRepository implements PushNotificationInterface
{
    use CastTranslationColumnTrait;

    private $model;

    public function __construct()
    {
        $this->model = new PushNotification();
    }


    public function create(array $data = [])
    {
        $this->model = $this->model->create($data);
        return $this;
    }


    public function update(array $data = [])
    {
        $this->model = $this->model->update($data);
        return $this;
    }



    public function readByName($name): ?PushTokenInterface
    {
        $this->model = $this->model->where('name', $name);
        return $this;
    }


    public function updateOrCreate(array $data = [])
    {
        $this->model = $this->model->updateOrCreate($data);
        return $this;
    }


    public function delete()
    {
        $this->model = $this->model->delete();
        return $this;
    }

    public function readById($id)
    {
        $this->model = $this->model->where('id', $id);
        return $this;
    }

    public function readByUnique($key, $value)
    {
        $this->model = $this->model->where($key, $value);
        return $this;
    }

    public function select(array $columns)
    {
        $this->model = $this->model->select($columns);
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

    public function orderBy($key, $method)
    {
        $this->model = $this->model->orderBy($key, $method);
        return $this;
    }
}
