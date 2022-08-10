<?php

namespace App\Services;

use App\Models\Recipient;
use App\Repositories\RecipientRepository;
use Illuminate\Support\Facades\Validator;

class RecipientService
{
    public const VALIDATION_RULES = [
        'email' => ['required', 'email', 'unique:recipients', 'max:255'],
        'first_name' => ['required', 'max:255'],
        'last_name' => ['required', 'max:255']
    ];

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
        $validated = $this->validate($data);
        $recipient = $this->recipientRepository->hydrate($validated);
        return $this->recipientRepository->save($recipient);
    }

    /**
     * @param array $data
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validate(array $data): array
    {
        $validator = Validator::make($data, self::VALIDATION_RULES);
        $validator->validate();
        return $validator->validated();
    }

}