<?php

namespace App\Jobs;

use App\Models\Mail;
use App\Services\MailDeliveryProviderService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendTransactionalEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public Mail $mail)
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $handler = new MailDeliveryProviderService();
        $this->mail->load(['recipient', 'content']);
        $providers = config('mail.providers');
        $result = false;
        foreach ($providers as $name => $provider) {
            Log::info("Trying {$name} for mail {$this->mail->id}");
            $result = $handler->setProvider($name)->send($this->mail);
            if ($result) {
                Log::info("{$name} worked for mail {$this->mail->id}");
                break;
            } else {
                Log::info("{$name} didn't work for mail {$this->mail->id}");
            }
        }
        if (!$result && $this->attempts() < config('mail.max_attempts')) {
            $this->release(config('mail.delay_attempt_for_seconds'));
        }
    }
}
