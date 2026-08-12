<?php
declare(strict_types=1);

namespace CommunicationCenter\Email;

use Cake\Mailer\Mailer;

/**
 * Sends emails using CakePHP Mailer.
 */
final class CakeEmailSender implements EmailSenderInterface
{
    /**
     * Constructor.
     *
     * @param string $mailerProfile CakePHP mailer profile.
     */
    public function __construct(
        private readonly string $mailerProfile = 'default',
    ) {
    }

    /**
     * @inheritDoc
     */
    public function send(
        string $to,
        string $subject,
        string $message,
    ): void {
        $mailer = new Mailer($this->mailerProfile);

        $mailer
            ->setTo($to)
            ->setSubject($subject)
            ->setEmailFormat('text')
            ->deliver($message);
    }
}
