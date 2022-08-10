<?php

namespace App\Repositories;

use App\Models\Recipient;


class RecipientRepository extends AbstractRepository implements RepositoryInterface
{
    protected string $model;

    public function __construct()
    {
        $this->model = Recipient::class;
    }
}