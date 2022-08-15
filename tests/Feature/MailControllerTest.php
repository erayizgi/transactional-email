<?php

namespace Tests\Feature;

use App\Jobs\SendTransactionalEmail;
use App\Models\Content;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\CreatesApplication;
use Tests\TestCase;

class MailControllerTest extends TestCase
{
    use RefreshDatabase, CreatesApplication;

    /**
     * A basic test example.
     * @dataProvider createMailDataProvider
     * @return void
     */
    public function test_create_mail(array $data)
    {
        Queue::fake();
        $response = $this->post('/api/v1/mail', [
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
        ]);
        Queue::assertPushed(SendTransactionalEmail::class);

        $response->assertStatus(200);
    }

    public function createMailDataProvider()
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
            ]

        ];
    }
}
