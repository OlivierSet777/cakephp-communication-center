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
     * @param string $template Email template.
     * @param string $layout Email layout.
     * @param array<string, mixed> $viewVars Additional view variables.
     */
    public function __construct(
        private readonly string $mailerProfile = 'default',
        private readonly string $template = 'CommunicationCenter.default',
        private readonly string $layout = 'default',
        private readonly array $viewVars = [],
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

        $viewVars = array_merge(
            $this->viewVars,
            [
                'subject' => $subject,
                'message' => $message,
            ],
        );

        $mailer
            ->setTo($to)
            ->setSubject($subject)
            ->setEmailFormat('html')
            ->setViewVars($viewVars);

        $mailer
            ->viewBuilder()
            ->setTemplate($this->template)
            ->setLayout($this->layout);

        $mailer->deliver();
    }
}
