<?php
// src/Service/MailerService.php
namespace App\Services;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly string          $fromAddress
    ) {}

    /**
     * Envoie un e-mail simple.
     *
     * @param string $to      Destinataire
     * @param string $subject Objet du mail
     * @param string $body    Contenu HTML ou texte
     */
    public function sendEmail(string $to, string $subject, string $body): void
    {
        $email = (new Email())
            ->from($this->fromAddress)
            ->to($to)
            ->subject($subject)
            ->html($body)
        ;

        $this->mailer->send($email);
    }
}
