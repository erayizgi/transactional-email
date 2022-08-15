<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

abstract class AbstractService
{

    abstract public function getValidationRules(): array;

    private bool $transactionStarted = false;

    protected final function startTransaction()
    {
        DB::beginTransaction();
        $this->transactionStarted = true;
    }

    protected final function commitTransaction()
    {
        if ($this->transactionStarted) {
            DB::commit();
            $this->transactionStarted = false;
        }
    }

    protected final function rollbackTransaction()
    {
        if ($this->transactionStarted) {
            DB::rollBack();
            $this->transactionStarted = false;
        }
    }

    /**
     * @param array $data
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validate(array $data): array
    {
        $validator = Validator::make($data, $this->getValidationRules());
        return $validator->validated();
    }
}