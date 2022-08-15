<?php

namespace App\Services\Mail;

use App\Models\Mail;

interface MailDeliveryAdapterInterface
{
    public function send(Mail $mail): bool;

    public function postSendActions(Mail $mail);

}