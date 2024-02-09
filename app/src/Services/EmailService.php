<?php

declare(strict_types=1);

namespace App\Services;

class EmailService
{

    public function __construct(protected array $config)
    {
    }

    public function sendEmail(string $to, string $subject, string $message): void
    {
        $smtp = $this->config['smtp'];

        echo "Email sent to $to with subject: $subject and message: $message.  <br />";
    }
}
