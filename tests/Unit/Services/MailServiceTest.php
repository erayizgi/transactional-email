<?php

namespace Tests\Unit\Services;

use App\Jobs\SendTransactionalEmail;
use App\Models\Content;
use App\Models\Mail;
use App\Models\Recipient;
use App\Repositories\MailRepository;
use App\Services\ContentService;
use App\Services\MailDeliveryProviderService;
use App\Services\MailService;
use App\Services\RecipientService;
use Illuminate\Support\Facades\Queue;
use Mockery\MockInterface;
use Tests\TestCase;

class MailServiceTest extends TestCase
{
    protected RecipientService|MockInterface $recipientService;
    protected ContentService|MockInterface $contentService;
    protected MailRepository|MockInterface $mailRepository;
    protected MailDeliveryProviderService|MockInterface $mailDeliveryProviderService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->recipientService            = $this->mock(RecipientService::class);
        $this->contentService              = $this->mock(ContentService::class);
        $this->mailRepository              = $this->mock(MailRepository::class);
        $this->mailDeliveryProviderService = $this->mock(MailDeliveryProviderService::class);
    }

    /**
     * @dataProvider inputProviderForCreate
     */
    public function test_create(array $input)
    {
        $recipientValidation = false;
        if (isset($input['recipients']) && is_array($input['recipients'])
            && array_keys($input['recipients'][0]) === ['email', 'first_name', 'last_name']) {
            $recipientValidation = true;
            $this->mockRecipientServiceForCreate($input['recipients']);
        } else {
            $input['recipients'] = [];
        }

        $contentValidation = false;
        if (isset($input['content']) && is_array($input['content'])
            && array_keys($input['content']) === ['content', 'content_type']) {
            $contentValidation = true;
            $this->mockContentServiceForCreate($input['content']['content'], count($input['recipients']));
        }

        $mailValidation = false;
        if (isset($input['mail']) && is_array($input['mail']) && isset($input['mail']['subject'])
            && strlen($input['mail']['subject']) <= 255 && $contentValidation && $recipientValidation) {
            $mailValidation = true;
            $this->mockMailRepositoryForCreate($input['mail'], count($input['recipients']));
        }

        $validationFails = !$recipientValidation || !$mailValidation || !$contentValidation;
        Queue::fake();
        if($validationFails) {
            $this->expectException(\Exception::class);
        }

        $service = new MailService($this->recipientService, $this->contentService, $this->mailRepository, $this->mailDeliveryProviderService);
        $result = $service->create($input);
        if ($validationFails) {
            Queue::assertNothingPushed();
        } else {
            Queue::assertPushed(SendTransactionalEmail::class);
        }
        if ($recipientValidation && $mailValidation && $contentValidation) {
            $this->assertEquals(count($input['recipients']), count($result));
        }
    }

    public function inputProviderForCreate(): array
    {
        return [
            [
                // correct data with multiple recipients
                [
                    'recipients' => [
                        [
                            'email' => fake('en_GB')->email,
                            'first_name' => fake('en_GB')->firstName,
                            'last_name' => fake('en_GB')->lastName
                        ],
                        [
                            'email' => fake('en_GB')->email,
                            'first_name' => fake('en_GB')->firstName,
                            'last_name' => fake('en_GB')->lastName
                        ]
                    ],
                    'content' => [
                        'content' => fake('en_GB')->randomHtml,
                        'content_type' => Content::CONTENT_TYPE_HTML
                    ],
                    'mail' => [
                        'subject' => fake('en_GB')->sentence
                    ]
                ]
            ],
            [
                // missing content key in content data
                [
                    'recipients' => [
                        [
                            'email' => fake('en_GB')->email,
                            'first_name' => fake('en_GB')->firstName,
                            'last_name' => fake('en_GB')->lastName
                        ]
                    ],
                    'content' => [
                        'content_type' => Content::CONTENT_TYPE_TEXT
                    ],
                    'mail' => [
                        'subject' => fake('en_GB')->sentence
                    ]
                ]
            ]

        ];
    }

    protected function mockContentServiceForCreate($content, $timesCalled = 0)
    {
        $this->contentService->shouldReceive('replaceVariables')
            ->times($timesCalled)
            ->withAnyArgs()
            ->andreturn($content);
        $this->contentService->shouldReceive('create')
            ->times($timesCalled)
            ->andReturnUsing(function ($content) {
                return new Content($content);
            });
    }

    protected function mockRecipientServiceForCreate($recipients)
    {
        $i = 1;
        $this->recipientService->shouldReceive('getOrCreate')
            ->times(count($recipients))
            ->andReturnUsing(function ($k) use (&$i) {
                $m = new Recipient($k);
                $m->id = $i;
                $i++;
                return $m;
            });
    }

    protected function mockMailRepositoryForCreate($mail, $timesCalled)
    {
        $mail['delivery_group_hash'] = fake()->uuid();
        $this->mailRepository->shouldReceive('hydrate')
            ->withAnyArgs()
            ->andReturn(new Mail($mail));
        $this->mailRepository->shouldReceive('save')
            ->times($timesCalled)
            ->andReturnusing(function ($mail) {
                return $mail;
            });
    }

}