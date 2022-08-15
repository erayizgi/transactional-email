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

    /**
     * @param \App\Models\Recipient $recipient
     * @return \App\Models\Recipient
     */
    public function firstOrCreate(Recipient $recipient): Recipient
    {
        return Recipient::firstOrCreate(
            ['email' => $recipient->email],
            $recipient->toArray()
        );
    }
}