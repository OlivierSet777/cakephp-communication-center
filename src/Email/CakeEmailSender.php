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
     * @param string|null $logoPath Optional embedded logo path.
     * @param string $logoCid Embedded logo CID.
     */
    public function __construct(
        private readonly string $mailerProfile = 'default',
        private readonly string $template = 'CommunicationCenter.default',
        private readonly string $layout = 'default',
        private readonly array $viewVars = [],
        private readonly ?string $logoPath = null,
        private readonly string $logoCid = 'communication-center-logo',
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
                'logoCid' => $this->logoCid,
            ],
        );

        $mailer
            ->setTo($to)
            ->setSubject($subject)
            ->setEmailFormat('html')
            ->setViewVars($viewVars);

        if (
            $this->logoPath !== null
            && is_file($this->logoPath)
        ) {
            $mailer->addAttachments([
                'communication-center-logo.png' => [
                    'file' => $this->logoPath,
                    'mimetype' => 'image/png',
                    'contentId' => $this->logoCid,
                ],
            ]);
        }

        $mailer
            ->viewBuilder()
            ->setTemplate($this->template)
            ->setLayout($this->layout);

        $mailer->deliver();
    }
}
