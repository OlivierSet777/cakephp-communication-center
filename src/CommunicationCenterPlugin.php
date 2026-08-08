<?php
declare(strict_types=1);

namespace CommunicationCenter;

use Cake\Core\BasePlugin;
use Cake\Core\ContainerInterface;
use CommunicationCenter\Channel\Registry\ChannelRegistry;
use CommunicationCenter\Channel\WhatsApp\WhatsAppChannel;
use CommunicationCenter\Message\MessageRendererInterface;
use CommunicationCenter\Message\SimpleMessageRenderer;
use CommunicationCenter\Recipient\Provider\Registry\RecipientProviderRegistry;
use CommunicationCenter\Service\CommunicationService;

/**
 * Plugin for Communication Center.
 */
class CommunicationCenterPlugin extends BasePlugin
{
    /**
     * Register application container services.
     *
     * @param \Cake\Core\ContainerInterface $container Container.
     * @return void
     */
    public function services(ContainerInterface $container): void
    {
        $container->addShared(ChannelRegistry::class, function () {
            $registry = new ChannelRegistry();

            $registry->set(new WhatsAppChannel());

            return $registry;
        });

        $container->addShared(RecipientProviderRegistry::class);

        $container->addShared(
            MessageRendererInterface::class,
            SimpleMessageRenderer::class,
        );

        $container->addShared(CommunicationService::class)
            ->addArgument(MessageRendererInterface::class);
    }
}
