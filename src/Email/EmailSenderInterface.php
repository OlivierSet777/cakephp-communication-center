<?php
declare(strict_types=1);

namespace CommunicationCenter\Email;

/**
 * Defines an email sender.
 */
interface EmailSenderInterface
{
    /**
     * Send an email.
     *
     * @param string $to Recipient email address.
     * @param string $subject Email subject.
     * @param string $message Email message.
     * @return void
     */
    public function send(
        string $to,
        string $subject,
        string $message,
    ): void;
}
