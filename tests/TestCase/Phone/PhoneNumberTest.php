<?php
declare(strict_types=1);

namespace CommunicationCenter\Test\TestCase\Phone;

use CommunicationCenter\Phone\PhoneNumber;
use PHPUnit\Framework\TestCase;

class PhoneNumberTest extends TestCase
{
    public function testNormalizesFrenchInternationalNumber(): void
    {
        $phone = new PhoneNumber('+33 6 12 34 56 78');

        $this->assertSame('+33612345678', $phone->normalized());
        $this->assertTrue($phone->isValid());
    }

    public function testNormalizesAustralianInternationalNumber(): void
    {
        $phone = new PhoneNumber('+61 412 345 678');

        $this->assertSame('+61412345678', $phone->normalized());
        $this->assertTrue($phone->isValid());
    }

    public function testNormalizesChineseInternationalNumber(): void
    {
        $phone = new PhoneNumber('+86 138 1234 5678');

        $this->assertSame('+8613812345678', $phone->normalized());
        $this->assertTrue($phone->isValid());
    }

    public function testAcceptsDoubleZeroInternationalPrefix(): void
    {
        $phone = new PhoneNumber('0033 6 12 34 56 78');

        $this->assertSame('+33612345678', $phone->normalized());
        $this->assertTrue($phone->isValid());
    }

    public function testRejectsLocalNumberWithoutCountryCode(): void
    {
        $phone = new PhoneNumber('06 12 34 56 78');

        $this->assertFalse($phone->isValid());
    }

    public function testRejectsInvalidValue(): void
    {
        $phone = new PhoneNumber('bonjour');

        $this->assertFalse($phone->isValid());
    }

    public function testReturnsWhatsAppFormat(): void
    {
        $phone = new PhoneNumber('+33 6 12 34 56 78');

        $this->assertSame('33612345678', $phone->forWhatsApp());
    }
}
