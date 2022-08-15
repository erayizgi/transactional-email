<?php

namespace App\Services;

use App\Models\Recipient;
use App\Repositories\RecipientRepository;

class RecipientService extends AbstractService
{
    public const VALIDATION_RULES = [
        'email' => ['required', 'email:rfc,strict', 'max:255'],
        'first_name' => ['required', 'max:255'],
        'last_name' => ['required', 'max:255']
    ];

    public function __construct(protected RecipientRepository $recipientRepository)
    {
    }

    /**
     * @param array{
     *         email: string,
     *         fist_name: string,
     *         last_name: string
     *     } $data
     * @return \App\Models\Recipient
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Throwable
     */
    public function getOrCreate(array $data): Recipient
    {
        $validated = $this->validate($data);
        $recipient = $this->recipientRepository->hydrate($validated);
        return $this->recipientRepository->firstOrCreate($recipient);
    }

    /**
     * @return \string[][]
     */
    public function getValidationRules(): array
    {
        return self::VALIDATION_RULES;
    }
}