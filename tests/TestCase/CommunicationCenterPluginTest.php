<?php
declare(strict_types=1);

namespace CommunicationCenter\Test\TestCase;

use CommunicationCenter\CommunicationCenterPlugin;
use PHPUnit\Framework\TestCase;

class CommunicationCenterPluginTest extends TestCase
{
    public function testPluginCanBeInstantiated(): void
    {
        $plugin = new CommunicationCenterPlugin();

        $this->assertInstanceOf(CommunicationCenterPlugin::class, $plugin);
    }
}
