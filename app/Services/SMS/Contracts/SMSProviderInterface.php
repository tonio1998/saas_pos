<?php

namespace App\Services\SMS\Contracts;

interface SMSProviderInterface
{
    public function send(
        string $phone,
        string $message
    ): bool;
}
