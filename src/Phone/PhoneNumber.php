<?php
declare(strict_types=1);

namespace CommunicationCenter\Phone;

/**
 * Represents and validates an international phone number.
 */
final readonly class PhoneNumber
{
    /**
     * Constructor.
     *
     * @param string $value International phone number.
     */
    public function __construct(
        public string $value,
    ) {
    }

    /**
     * Checks whether the phone number uses a valid international format.
     *
     * @return bool
     */
    public function isValid(): bool
    {
        return preg_match(
            '/^\+[1-9][0-9]{7,14}$/',
            $this->normalized(),
        ) === 1;
    }

    /**
     * Returns the normalized international phone number.
     *
     * @return string
     */
    public function normalized(): string
    {
        $value = trim($this->value);

        if (str_starts_with($value, '00')) {
            $value = '+' . substr($value, 2);
        }

        $digits = preg_replace(
            '/\D+/',
            '',
            ltrim($value, '+'),
        ) ?? '';

        return '+' . $digits;
    }

    /**
     * Returns the number in WhatsApp format.
     *
     * @return string
     */
    public function forWhatsApp(): string
    {
        return ltrim($this->normalized(), '+');
    }
}
