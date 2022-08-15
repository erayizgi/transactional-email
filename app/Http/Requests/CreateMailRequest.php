<?php

namespace App\Http\Requests;

use App\Models\Content;
use App\Services\ContentService;
use App\Services\MailService;
use App\Services\RecipientService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateMailRequest extends FormRequest
{
    protected function prepareRules(string $key, array $rules): array
    {
        foreach ($rules as $field => $rule) {
            $validationRules[$key.$field] = $rule;
        }
        return $validationRules;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $contentRules = ContentService::VALIDATION_RULES;
        unset($contentRules['recipient_id']);
        $contentRules['content_type'] = ["required", Rule::in(array_values(Content::CONTENT_TYPES))];
        $recipientRules = $this->prepareRules("recipients.*.", RecipientService::VALIDATION_RULES);
        $contentRules = $this->prepareRules("content.", $contentRules);
        $mailRules = $this->prepareRules("mail.", MailService::VALIDATION_RULES);
        return [
            'recipients' => ['array','required'],
            ...$recipientRules,
            'content' => ['array', 'required'],
            ...$contentRules,
            'mail' => ['array', 'required'],
            ...$mailRules
        ];
    }
}
