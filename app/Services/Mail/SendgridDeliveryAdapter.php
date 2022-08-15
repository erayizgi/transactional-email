<?php

namespace App\Services\Mail;

use App\Models\Mail;
use App\Services\MailService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use SendGrid;
use SendGrid\Mail\Mail as SendGridMail;

class SendgridDeliveryAdapter implements MailDeliveryAdapterInterface
{
    public const PROVIDER = 'sendgrid';
    protected \SendGrid $sendGridClient;
    protected SendGridMail $sendGridMail;
    protected string $fromMail;
    protected string $fromName;

    public function __construct()
    {
        $this->sendGridClient = new SendGrid(config('mail.credentials.sendgrid.api_key'));
        $this->sendGridMail = new SendGridMail();
        $this->fromMail = config('mail.from.address');
        $this->fromName = config('mail.from.name');
    }

    /**
     * @param \App\Models\Mail $mail
     * @return bool
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \SendGrid\Mail\TypeException
     * @throws \Throwable
     */
    public function send(Mail $mail): bool
    {
        $this->sendGridMail->setFrom($this->fromMail, $this->fromName);
        $this->sendGridMail->addTo($mail->recipient->email, "{$mail->recipient->first_name} {$mail->recipient->last_name}");
        $this->sendGridMail->setSubject($mail->subject);
        $this->sendGridMail->addContent($mail->content->content_type, $mail->content->content);
        try{
            $response = $this->sendGridClient->send($this->sendGridMail);
            if($response->statusCode() >=200 && $response->statusCode()<=299) {
                Log::info("Mail with {$mail->id} id has been sent with sendgrid.");
                $this->postSendActions($mail);
                return true;
            } else {
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