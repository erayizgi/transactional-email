<?php

namespace App\Repositories;

use App\Models\Mail;

class MailRepository extends AbstractRepository
{
    protected string $model;

    public function __construct()
    {
        $this->model = Mail::class;
    }
}