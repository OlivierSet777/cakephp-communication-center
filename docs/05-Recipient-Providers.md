# Recipient providers

## Purpose

A recipient provider translates host application data into normalized recipient records.

## Contract proposal

```php
interface RecipientProviderInterface
{
    public function getName(): string;

    public function getAvailableFilters(): array;

    public function findRecipients(array $filters = []): iterable;
}
```

## Normalized recipient

```php
final readonly class RecipientData
{
    public function __construct(
        public string $externalId,
        public ?int $userId,
        public ?string $firstname,
        public ?string $lastname,
        public ?string $phone,
        public ?string $email,
        public array $variables = [],
    ) {
    }
}
```

## host application provider

The host application integration will remain in the host application.

It will expose filters such as:

- recycling center;
- active member status;
- contribution month;
- unpaid contribution;
- day of attendance.
