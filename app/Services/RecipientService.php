<?php

namespace App\Services;

use App\Models\Recipient;
use App\Repositories\RecipientRepository;
use Illuminate\Support\Facades\Validator;

class RecipientService
{
    private array $validationRules = [
        'email' => ['required', 'email', 'unique:recipients', 'max:255'],
        'first_name' => ['required', 'max:255'],
        'last_name' => ['required', 'max:255']
    ];

    private array $validated;

    public function __construct(protected RecipientRepository $recipientRepository)
    {
    }

    /**
     * @param array $data
     * @return \App\Models\Recipient
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Throwable
     */
    public function create(array $data): Recipient
    {
        $this->validate($data);
        $recipient = $this->recipientRepository->hydrate($this->getValidated());
        return $this->recipientRepository->save($recipient);
    }

    /**
     * @return array
     */
    private function getValidated(): array
    {
        return $this->validated;
    }

    /**
     * @param array $validated
     */
    private function setValidated(array $validated): void
    {
        $this->validated = $validated;
    }

    /**
     * @param array $data
     * @return \App\Services\RecipientService
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validate(array $data): self
    {
        $validator = Validator::make($data, $this->validationRules);
        $validator->validate();
        $this->setValidated($validator->validated());
        return $this;
    }

}