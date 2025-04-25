<?php

namespace App\Services;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class MailerService
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly string $fromAddress
    ) {}

    public function sendWelcomeEmail(string $to, string $name): void
    {
        $email = (new TemplatedEmail())
            ->from($this->fromAddress)
            ->to($to)
            ->subject('Bienvenue sur notre plateforme')
            ->htmlTemplate('email/welcome.html.twig')
            ->context([
                'name' => $name,
            ]);

        $this->mailer->send($email);
    }
}