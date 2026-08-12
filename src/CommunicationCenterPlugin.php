<?php
declare(strict_types=1);

namespace CommunicationCenter;

use Cake\Core\BasePlugin;
use Cake\Core\ContainerInterface;
use Cake\ORM\Locator\TableContainer;
use CommunicationCenter\Channel\Email\EmailChannel;
use CommunicationCenter\Channel\Registry\ChannelRegistry;
use CommunicationCenter\Channel\WhatsApp\WhatsAppChannel;
use CommunicationCenter\Email\CakeEmailSender;
use CommunicationCenter\Email\EmailSenderInterface;
use CommunicationCenter\Message\MessageRendererInterface;
use CommunicationCenter\Message\SimpleMessageRenderer;
use CommunicationCenter\Model\Table\CommunicationCampaignsTable;
use CommunicationCenter\Recipient\Provider\Registry\RecipientProviderRegistry;
use CommunicationCenter\Service\CampaignService;
use CommunicationCenter\Service\CommunicationService;
use CommunicationCenter\Service\EmailCampaignService;

/**
 * Plugin for Communication Center.
 */
class CommunicationCenterPlugin extends BasePlugin
{
    /**
     * Whether the plugin has routes.
     *
     * @var bool
     */
    protected bool $routesEnabled = true;

    /**
     * Register application container services.
     *
     * @param \Cake\Core\ContainerInterface $container Container.
     * @return void
     */
    public function services(ContainerInterface $container): void
    {
        $container->delegate(
            new TableContainer(),
        );

        $container->addShared(ChannelRegistry::class, function () {
            $registry = new ChannelRegistry();

            $registry->set(new WhatsAppChannel());
            $registry->set(new EmailChannel());

            return $registry;
        });

        $container->addShared(RecipientProviderRegistry::class);

        $container->addShared(
            MessageRendererInterface::class,
            SimpleMessageRenderer::class,
        );

        $container->addShared(CommunicationService::class)
            ->addArgument(MessageRendererInterface::class);

        $container->addShared(CampaignService::class)
            ->addArgument(CommunicationCampaignsTable::class);

        $container->addShared(
            EmailSenderInterface::class,
            CakeEmailSender::class,
        );

        $container->addShared(EmailCampaignService::class)
            ->addArgument(CampaignService::class)
            ->addArgument(EmailSenderInterface::class);
    }
}
