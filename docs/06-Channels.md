# Channels

## Contract proposal

```php
interface ChannelInterface
{
    public function getName(): string;

    public function supports(string $recipientField): bool;

    public function prepare(
        RecipientData $recipient,
        RenderedMessage $message
    ): ChannelAction;
}
```

## WhatsApp V1

The first WhatsApp channel does not send messages automatically.

It generates a link such as:

```text
https://wa.me/<international-phone>?text=<encoded-message>
```

The user remains responsible for pressing the Send button in WhatsApp.

## Future channels

- EmailChannel
- SmsChannel
- NotificationChannel
- WebhookChannel
