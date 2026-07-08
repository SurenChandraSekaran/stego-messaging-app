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

        // 🌟 FIX: Convert recipients and enforce a non-empty name string
        $to = [];
        foreach ($email->getTo() as $address) {
            $recipientName = trim($address->getName());
            $to[] = [
                'email' => $address->getAddress(),
                'name' => !empty($recipientName) ? $recipientName : 'Chat User'
            ];
        }

        $fromAddress = $email->getFrom()[0] ?? null;
        $fromName = $fromAddress ? trim($fromAddress->getName()) : '';
        
        $from = [
            'email' => $fromAddress ? $fromAddress->getAddress() : config('mail.from.address'),
            'name' => !empty($fromName) ? $fromName : config('mail.from.name', 'StegoApp Admin')
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
        ])->throw();
    }

    public function __toString(): string
    {
        return 'brevo';
    }
}