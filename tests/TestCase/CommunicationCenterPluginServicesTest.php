<?php
declare(strict_types=1);

namespace CommunicationCenter\Test\TestCase;

use Cake\Core\Container;
use CommunicationCenter\Channel\Registry\ChannelRegistry;
use CommunicationCenter\Channel\WhatsApp\WhatsAppChannel;
use CommunicationCenter\CommunicationCenterPlugin;
use CommunicationCenter\Message\MessageRendererInterface;
use CommunicationCenter\Message\SimpleMessageRenderer;
use CommunicationCenter\Recipient\Provider\Registry\RecipientProviderRegistry;
use CommunicationCenter\Service\CampaignService;
use CommunicationCenter\Service\CommunicationService;
use League\Container\ReflectionContainer;
use PHPUnit\Framework\TestCase;

class CommunicationCenterPluginServicesTest extends TestCase
{
    /**
     * Test CampaignService can be resolved from the container.
     *
     * @return void
     */
    public function testCampaignServiceCanBeResolved(): void
    {
        $container = new Container();

        $plugin = new CommunicationCenterPlugin();
        $plugin->services($container);

        $container->delegate(
            new ReflectionContainer(),
        );

        $service = $container->get(
            CampaignService::class,
        );

        $this->assertInstanceOf(
            CampaignService::class,
            $service,
        );
    }

    public function testPluginRegistersServices(): void
    {
        $container = new Container();

        $plugin = new CommunicationCenterPlugin();
        $plugin->services($container);

        $container->delegate(
            new ReflectionContainer(),
        );

        $renderer = $container->get(MessageRendererInterface::class);
        $service = $container->get(CommunicationService::class);
        $channels = $container->get(ChannelRegistry::class);
        $providers = $container->get(RecipientProviderRegistry::class);

        $this->assertInstanceOf(
            SimpleMessageRenderer::class,
            $renderer,
        );

        $this->assertInstanceOf(
            CommunicationService::class,
            $service,
        );

        $this->assertInstanceOf(
            ChannelRegistry::class,
            $channels,
        );

        $this->assertInstanceOf(
            RecipientProviderRegistry::class,
            $providers,
        );
    }

    public function testWhatsAppChannelIsRegisteredByDefault(): void
    {
        $container = new Container();

        $plugin = new CommunicationCenterPlugin();
        $plugin->services($container);

        $channels = $container->get(ChannelRegistry::class);

        $this->assertTrue(
            $channels->has('whatsapp'),
        );

        $this->assertInstanceOf(
            WhatsAppChannel::class,
            $channels->get('whatsapp'),
        );
    }

    public function testRegistriesAreShared(): void
    {
        $container = new Container();

        $plugin = new CommunicationCenterPlugin();
        $plugin->services($container);

        $first = $container->get(ChannelRegistry::class);
        $second = $container->get(ChannelRegistry::class);

        $this->assertSame($first, $second);

        $firstProviders = $container->get(
            RecipientProviderRegistry::class,
        );

        $secondProviders = $container->get(
            RecipientProviderRegistry::class,
        );

        $this->assertSame(
            $firstProviders,
            $secondProviders,
        );
    }
}
