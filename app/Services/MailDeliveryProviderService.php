<?php

namespace App\Services;

use App\Models\Mail;
use App\Services\Mail\MailDeliveryAdapterInterface;

class MailDeliveryProviderService
{
    protected MailDeliveryAdapterInterface $provider;

    /**
     * @param string $provider
     * @return $this
     */
    public function setProvider(string $provider): static
    {
        $providerClass = config("mail.providers.{$provider}");
        $this->provider = new $providerClass();
        return $this;
    }

    public function send(Mail $mail): bool
    {
        return $this->provider->send($mail);
    }

}