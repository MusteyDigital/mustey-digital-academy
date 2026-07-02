<?php

namespace App\Mail\Transport;

use Illuminate\Support\Facades\Http;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\MessageConverter;

class BrevoApiTransport extends AbstractTransport
{
    public function __construct(private readonly string $apiKey)
    {
        parent::__construct();
    }

    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());

        $payload = $this->buildPayload($email);

        $response = Http::withHeaders([
            'api-key' => $this->apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post('https://api.brevo.com/v3/smtp/email', $payload);

        if ($response->failed()) {
            throw new TransportException(
                'Brevo API mail send failed: ' . $response->status() . ' ' . $response->body()
            );
        }
    }

    private function buildPayload(Email $email): array
    {
        $from = $email->getFrom()[0] ?? null;

        $payload = [
            'sender' => [
                'email' => $from?->getAddress() ?? config('mail.from.address'),
                'name' => $from?->getName() ?: config('mail.from.name'),
            ],
            'to' => array_map(
                fn ($address) => array_filter([
                    'email' => $address->getAddress(),
                    'name' => $address->getName() ?: null,
                ]),
                $email->getTo()
            ),
            'subject' => $email->getSubject(),
        ];

        if ($html = $email->getHtmlBody()) {
            $payload['htmlContent'] = $html;
        }

        if ($text = $email->getTextBody()) {
            $payload['textContent'] = $text;
        }

        if ($replyTo = $email->getReplyTo()) {
            $first = $replyTo[0];
            $payload['replyTo'] = array_filter([
                'email' => $first->getAddress(),
                'name' => $first->getName() ?: null,
            ]);
        }

        if ($cc = $email->getCc()) {
            $payload['cc'] = array_map(
                fn ($address) => array_filter([
                    'email' => $address->getAddress(),
                    'name' => $address->getName() ?: null,
                ]),
                $cc
            );
        }

        if ($bcc = $email->getBcc()) {
            $payload['bcc'] = array_map(
                fn ($address) => array_filter([
                    'email' => $address->getAddress(),
                    'name' => $address->getName() ?: null,
                ]),
                $bcc
            );
        }

        return $payload;
    }

    public function __toString(): string
    {
        return 'brevo+api://api.brevo.com';
    }
}
