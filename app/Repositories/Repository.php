<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class Repository
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * Repository constructor.
     * @param $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @param array $data
     * @param string $uniqueField
     * @return mixed
     */
    public function createIfNotExists(array $data, string $uniqueField)
    {
        $model = $this->model->where('ext_id', $data[$uniqueField])->first();

        if (!$model) {
            $model = $this->model->create($data);
        }

        return $model->id;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public function findBy(string $key, $value)
    {
        return $this->model->where($key, $value)->first();
    }
}