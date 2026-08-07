<?php
declare(strict_types=1);

namespace CommunicationCenter\Message;

use CommunicationCenter\Recipient\Recipient;

/**
 * Renders simple recipient variables inside message templates.
 */
final class SimpleMessageRenderer implements MessageRendererInterface
{
    /**
     * @inheritDoc
     */
    public function render(string $template, Recipient $recipient): string
    {
        $variables = array_merge(
            $recipient->variables,
            [
                'externalId' => $recipient->externalId,
                'firstname' => $recipient->firstname ?? '',
                'lastname' => $recipient->lastname ?? '',
                'phone' => $recipient->phone ?? '',
                'email' => $recipient->email ?? '',
            ],
        );

        $replacements = [];

        foreach ($variables as $name => $value) {
            if (!is_scalar($value) && $value !== null) {
                continue;
            }

            $replacements['{{' . $name . '}}'] = (string)($value ?? '');
        }

        return strtr($template, $replacements);
    }
}
