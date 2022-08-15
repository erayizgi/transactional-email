<?php

namespace App\Services;

use App\Models\Content;
use App\Repositories\ContentRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ContentService extends AbstractService
{
    public const VALIDATION_RULES = [
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

    /**
     * @param string $content
     * @param array $values
     * @return string
     */
    public function replaceVariables(string $content, array $values): string
    {
        foreach ($values as $key => $value) {
            $content = str_replace("{{{$key}}}", $value, $content);
        }
        return $content;
    }

    /**
     * @return \string[][]
     */
    public function getValidationRules(): array
    {
        return self::VALIDATION_RULES;
    }
}