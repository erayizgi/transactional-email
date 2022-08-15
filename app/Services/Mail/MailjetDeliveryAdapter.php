<?php

namespace App\Services\Mail;

use App\Models\Content;
use App\Models\Mail;
use App\Services\MailService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Mailjet\Client;
use Mailjet\Resources;

class MailjetDeliveryAdapter implements MailDeliveryAdapterInterface
{
    public const PROVIDER = 'mailjet';
    protected Client $mailJetClient;
    protected string $fromMail;
    protected string $fromName;

    public function __construct()
    {
        $this->mailJetClient = new Client(
            config('mail.credentials.mailjet.api_key'),
            config('mail.credentials.mailjet.api_secret'),
            true,
            ['version' => 'v3.1']
        );
        $this->fromMail      = config('mail.from.address');
        $this->fromName      = config('mail.from.name');
    }

    /**
     * @param \App\Models\Mail $mail
     * @return bool
     * @throws \Throwable
     */
    public function send(Mail $mail): bool
    {
        $htmlContent = '';
        $textContent = '';

        if ($mail->content->content_type === Content::CONTENT_TYPE_HTML) {
            $htmlContent = $mail->content->content;
        }

        if ($mail->content->content_type === Content::CONTENT_TYPE_TEXT) {
            $textContent = $mail->content->content;
        }

        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => $this->fromMail,
                        'Name' => $this->fromName
                    ],
                    'To' => [
                        [
                            'Email' => $mail->recipient->email,
                            'Name' => "{$mail->recipient->first_name} {$mail->recipient->last_name}"
                        ]
                    ],
                    'Subject' => $mail->subject,
                    'TextPart' => $textContent,
                    'HTMLPart' => $htmlContent,
                    'CustomID' => "AppGettingStartedTest"
                ]
            ]
        ];
        try {
            $response = $this->mailJetClient->post(Resources::$Email, ['body' => $body]);
            if ($response->success()) {
                $responseBody = json_encode($response->getBody());
                Log::info("Mail with {$mail->id} id has been sent with mailjet.");
                $this->postSendActions($mail);
                return true;
            } else {
                $errorBody = json_encode($response->getBody());
                Log::error("{$response->getReasonPhrase()} {$errorBody}");
                return false;
            }
        } catch (\Exception $e) {
            Log::info("We couldn't send the mail with {$mail->id} mail id. Something went wrong on provider side");
            throw $e;
        }

    }

    /**
     * @param \App\Models\Mail $mail
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Throwable
     */
    public function postSendActions(Mail $mail): void
    {
        $mail->provider = self::PROVIDER;
        $mail->sent_at = Carbon::now();
        $mailService = app()->make(MailService::class);
        $mailService->save($mail);
    }
}