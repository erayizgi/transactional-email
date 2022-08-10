<?php

namespace App\Repositories;

use App\Models\Content;

class ContentRepository extends AbstractRepository
{
    protected string $model;

    public function __construct()
    {
        $this->model = Content::class;
    }
}