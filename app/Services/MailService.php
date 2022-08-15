<?php

namespace App\Services;

use App\Jobs\SendTransactionalEmail;
use App\Models\Mail;
use App\Repositories\MailRepository;
use Ramsey\Uuid\Uuid;

class MailService extends AbstractService
{
    public const VALIDATION_RULES = [
        'subject' => ['required', 'string', 'max:255']
    ];

    public function __construct(
        protected RecipientService            $recipientService,
        protected ContentService              $contentService,
        protected MailRepository              $mailRepository,
        protected MailDeliveryProviderService $deliveryProviderService
    )
    {
    }


    /**
     * @param array{
     *     recipients: array{
     *         array{
     *             email: string,
     *             fist_name: string,
     *             last_name: string
     *         }
     *     },
     *     content: array{
     *         content: string,
     *         content_type: string
     *     },
     *     mail: array{
     *         subject: string
     *     }
     * } $data
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Throwable
     */
    public function create(array $data): array
    {
        $validatedMail                        = $this->validate($data['mail']);
        $deliveryGroup                        = Uuid::uuid4();
        $validatedMail['delivery_group_hash'] = $deliveryGroup;
        /** @var \App\Models\Mail $mail */
        $recipients = [];
        foreach ($data['recipients'] as $recipient) {
            $recipients[] = $this->recipientService->getOrCreate($recipient);
        }

        $createdMails = [];
        try {
            $this->startTransaction();
            foreach ($recipients as $recipient) {
                $mail                            = $this->mailRepository->hydrate($validatedMail);
                $replaceableValues               = [
                    'first_name' => $recipient->first_name,
                    'last_name' => $recipient->last_name
                ];
                $data['content']['content']      = $this->contentService->replaceVariables($data['content']['content'], $replaceableValues);
                $data['content']['recipient_id'] = $recipient->id;
                $content                         = $this->contentService->create($data['content']);
                $mail->recipient_id              = $recipient->id;
                $mail->content_id                = $content->id;
                $mail->delivery_group_hash       = $deliveryGroup;
                $mail                            = $this->mailRepository->save($mail);
                $createdMails[]                  = $mail;
                SendTransactionalEmail::dispatch($mail)->onConnection('rabbitmq');
                $this->commitTransaction();
            }
            return $createdMails;
        } catch (\Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }
    }

    /**
     * @return \string[][]
     */
    public function getValidationRules(): array
    {
        return self::VALIDATION_RULES;
    }

    /**
     * @param \App\Models\Mail $mail
     * @return \App\Models\Recipient|\Illuminate\Database\Eloquent\Model
     * @throws \Throwable
     */
    public function save(Mail $mail)
    {
        return $this->mailRepository->save($mail);
    }
}