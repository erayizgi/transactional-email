<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class AbstractRepository implements RepositoryInterface
{
    protected string $model;

    public function hydrate(array $data): Model
    {
        return new $this->model($data);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function findById(int $id): Model
    {
        return $this->model::findOrFail($id);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return \App\Models\Recipient
     * @throws \Throwable
     */
    public function save(Model $model): Model
    {
        $model->saveOrFail();
        return $model->refresh();
    }

}