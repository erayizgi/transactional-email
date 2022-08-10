<?php

namespace App\Services;

use App\Models\Content;
use App\Repositories\ContentRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ContentService
{

    public const VALIDATION_RULES = [
        'subject' => ['required', 'max:255'],
        'content' => ['required'],
        'recipient_id' => ['required', 'exists:recipients,id']
    ];

    public function __construct(protected ContentRepository $contentRepository)
    {

    }

    /**
     * @param array $data
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validate(array $data): array
    {
        $validator = Validator::make($data, self::VALIDATION_RULES);
        // Special condition needs to be added just before validation because validation rules are constant
        $validator->addRules([
            'content_type' => ['required', Rule::in(array_values(Content::CONTENT_TYPES))]
        ]);
        return $validator->validate();
    }

    /**
     * @param array $data
     * @return \App\Models\Content
     * @throws \Throwable
     */
    public function create(array $data): Content
    {
        $validated = $this->validate($data);
        $content = $this->contentRepository->hydrate($validated);
        return $this->contentRepository->save($content);
    }


}