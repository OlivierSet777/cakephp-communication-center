# Architecture

## Main layers

### Core domain

- Campaign
- CampaignRecipient
- MessageTemplate
- DeliveryAttempt

### Application services

- CampaignService
- RecipientSelectionService
- MessageRenderer
- ChannelManager
- DeliveryHistoryService

### Extension contracts

- RecipientProviderInterface
- ChannelInterface
- TemplateRendererInterface

### Infrastructure

- CakePHP ORM repositories
- WhatsApp deep-link driver
- configuration registry
- queue adapter in a later version

## Dependency rule

The plugin must never depend on any specific host application or business domain.

The host application may depend on the plugin and provide adapters.

## Initial workflow

1. The host application supplies a recipient provider.
2. The provider returns normalized recipient records.
3. The administrator selects recipients.
4. A campaign and recipient snapshots are persisted.
5. The message is rendered for the current recipient.
6. The WhatsApp driver generates a deep link.
7. The administrator returns to the campaign and marks the recipient as processed.
