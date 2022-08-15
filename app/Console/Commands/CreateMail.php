<?php

namespace App\Console\Commands;

use App\Models\Content;
use App\Services\MailService;
use App\Services\RecipientService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CreateMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:mail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a mail to be send from given input';
    protected array $recipients = [];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(MailService $mailService)
    {
        $this->line('Welcome to mail creator wizard!');
        $input['mail']['subject']         = $this->retrieveSubject();
        $input['content']['content_type'] = $this->choice('Please select the type of content you are sending', Content::CONTENT_TYPES);
        $input['content']['content']      = $this->ask('Please enter your content');
        $this->retrieveRecipients();
        $input['recipients'] = $this->recipients;
        $mails               = $mailService->create($input);
        foreach ($mails as $mail) {
            $this->info("Mail with {$mail->id} to be sent to {$mail->recipient->email} has been created");
        }
        return 0;
    }

    protected function retrieveRecipients()
    {
        $recipient['email']      = $this->ask('Please enter recipient email address');
        $recipient['first_name'] = $this->ask('Please enter recipients first name');
        $recipient['last_name']  = $this->ask('Please enter recipients first name');
        $validator               = Validator::make($recipient, RecipientService::VALIDATION_RULES);
        if ($validator->fails()) {
            $this->outputValidationErrors($validator);
            $this->retrieveRecipients();
        } else {
            $this->recipients[] = $validator->validated();
            $this->info('Recipient has been added!');
            if ($this->confirm('Do you want to add another recipient?', true)) {
                $this->retrieveRecipients();
            }
        }
        return $this->recipients;

    }

    protected function retrieveSubject()
    {
        $input['subject'] = $this->ask('Please enter the subject of your email (maximum allowed length 255 chars)');
        $subjectValidator = Validator::make($input, MailService::VALIDATION_RULES);
        if ($subjectValidator->fails()) {
            $this->outputValidationErrors($subjectValidator);
            $this->retrieveSubject();
        } else {
            return $subjectValidator->validated()['subject'];
        }
    }

    private function outputValidationErrors(\Illuminate\Contracts\Validation\Validator $validator)
    {
        foreach ($validator->errors()->all() as $error) {
            $this->error($error);
        }
    }
}
