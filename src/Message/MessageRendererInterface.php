<?php
declare(strict_types=1);

namespace CommunicationCenter\Message;

use CommunicationCenter\Recipient\Recipient;

/**
 * Defines how message templates are rendered for a recipient.
 */
interface MessageRendererInterface
{
    /**
     * Renders a message template for a recipient.
     *
     * @param string $template Message template.
     * @param \CommunicationCenter\Recipient\Recipient $recipient Recipient.
     * @return string
     */
    public function render(string $template, Recipient $recipient): string;
}
