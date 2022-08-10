<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

interface RepositoryInterface
{
    /**
     * Receive values as array, creates Model instance and returns it
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function hydrate(array $data): Model;

    /**
     * Get entity by id
     * @param int $id
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @return mixed
     */
    public function findById(int $id): Model;

    /**
     * Stores entity into database, refreshes the model, returns fresh model
     * If something goes wrong throws an exception
     * @param \Illuminate\Database\Eloquent\Model $model
     * @throws \Throwable
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function save(Model $model): Model;

}