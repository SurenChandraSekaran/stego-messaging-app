<?php

namespace App\Mail\Transports;

use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mime\Email;
use Illuminate\Support\Facades\Http;

class BrevoTransport extends AbstractTransport
{
    protected string $apiKey;

    public function __construct(string $apiKey)
    {
        parent::__construct();
        $this->apiKey = $apiKey;
    }

    protected function doSend(SentMessage $message): void
    {
        $email = $message->getOriginalMessage();

        if (!$email instanceof Email) {
            return;
        }

        // Convert Laravel/Symfony recipients into Brevo API format
        $to = [];
        foreach ($email->getTo() as $address) {
            $to[] = [
                'email' => $address->getAddress(),
                'name' => $address->getName() ?? ''
            ];
        }

        $fromAddress = $email->getFrom()[0] ?? null;
        $from = $fromAddress ? [
            'email' => $fromAddress->getAddress(),
            'name' => $fromAddress->getName() ?? config('mail.from.name')
        ] : [
            'email' => config('mail.from.address'),
            'name' => config('mail.from.name')
        ];

        // Execute API call over regular secure HTTPS (Port 443)
        Http::withHeaders([
            'api-key' => $this->apiKey,
            'accept' => 'application/json',
            'content-type' => 'application/json',
        ])->post('https://api.brevo.com/v3/smtp/email', [
            'sender' => $from,
            'to' => $to,
            'subject' => $email->getSubject(),
            'htmlContent' => $email->getHtmlBody() ?? $email->getTextBody(),
        ]);
    }

    public function __toString(): string
    {
        return 'brevo';
    }
}