# CakePHP Communication Center

A reusable, mobile-first communication hub for CakePHP 5 applications.

## Vision

The plugin centralizes campaigns, recipients, templates, channels and delivery history without depending on any application's business model.

The plugin does not know what a volunteer, member, customer or citizen is. It only works with recipients exposed by a recipient provider.

## Example use case

A typical integration can:

- expose recipients from the host application's business model;
- apply application-specific filters;
- select recipients from a mobile interface;
- prepare individualized WhatsApp or Email messages;
- process recipients individually or through supported bulk actions;
- preserve campaign progress and delivery history.

## Principles

- CakePHP 5 compatible
- PHP 8.1+
- mobile first
- Bootstrap 5 CSS required by the V1 interface
- CakePHP Bootstrap-UI is not required
- no mandatory jQuery dependency
- business-agnostic core
- extensible channels and recipient providers
- English code, translatable user interface
- tests and documentation from the beginning

## Current status

Communication Center V1 is functional and has been validated in a separate CakePHP sandbox application.

Currently supported:

- WhatsApp campaigns
- Email campaigns
- persistent campaigns and recipient snapshots
- reusable message templates
- recipient filtering supplied by the host application
- personalized message variables
- campaign progress tracking
- campaign history
- archive and restore
- responsive Bootstrap 5 interface
- automated tests and CakePHP coding-standard checks

## V1 interface dependency

The interface provided by Communication Center uses Bootstrap 5 CSS classes.
The host application must therefore load **Bootstrap 5 CSS** to get the intended layout and presentation.

The CakePHP **Bootstrap-UI plugin is not required**. Communication Center does not depend on its helpers; only the Bootstrap 5 stylesheet is required.

Without Bootstrap 5 CSS, the plugin functionality remains available, but the interface will not have the intended layout and styling.

A future version may make the interface fully self-contained and remove this visual dependency.

## Installation

Load the plugin in the CakePHP host application:

```bash
bin/cake plugin load CommunicationCenter
```

Run the plugin migrations:

```bash
bin/cake migrations migrate -p CommunicationCenter
```

Check migration status:

```bash
bin/cake migrations status -p CommunicationCenter
```

The plugin provides its routes under:

```text
/communication-center
```

## Host application integration

Communication Center remains independent from the business model of the host application.

The plugin provides:

- `ChannelRegistry`
- `RecipientProviderRegistry`
- `CommunicationService`
- `CampaignService`
- `EmailCampaignService`
- `WhatsAppChannel`
- `EmailChannel`
- campaigns and recipient persistence
- message templates
- the Communication Center UI

The host application provides:

- recipient data
- business-specific filtering
- SMTP configuration
- one or more Recipient Providers

A host application should not require Communication Center to directly know its
Users, Members, Volunteers or other business entities.

### Registering a Recipient Provider

The host application registers its provider in `Application::services()`:

```php
use App\CommunicationCenter\AppRecipientProvider;
use CommunicationCenter\Recipient\Provider\Registry\RecipientProviderRegistry;

$container->addShared(
    RecipientProviderRegistry::class,
    function (): RecipientProviderRegistry {
        $registry = new RecipientProviderRegistry();

        $registry->set(
            'app',
            new AppRecipientProvider(),
        );

        return $registry;
    },
);
```

For business-specific filters, the provider can implement
`FilterableRecipientProviderInterface`. The application remains responsible for
defining and applying those filters.

## Message templates

V1 includes reusable message templates.

A template contains a name, a channel, an optional Email subject, a message body
and an active/inactive status.

Only active templates matching the selected campaign channel are proposed.
Selecting a template pre-fills the campaign subject and message while keeping
them editable before preparation.

Templates are managed under:

```text
/communication-center/templates
```

## Email configuration

SMTP configuration belongs to the host CakePHP application. Communication Center
uses the host application's CakePHP Email configuration through `CakeEmailSender`
and does not store SMTP credentials itself.

## Development checks

Run the coding-standard checks:

```bash
composer cs-check
```

Apply automatically fixable coding-standard corrections:

```bash
composer cs-fix
```

Run the automated tests:

```bash
composer test
```

Before committing changes, the expected baseline is:

```bash
composer cs-check
composer test
```

Both commands should pass.

## Documentation

- [Vision](docs/01-Vision.md)
- [Scope](docs/02-Scope.md)
- [Architecture](docs/03-Architecture.md)
- [Database](docs/04-Database.md)
- [Recipient providers](docs/05-Recipient-Providers.md)
- [Channels](docs/06-Channels.md)
- [User interface](docs/07-User-Interface.md)
- [Roadmap](ROADMAP.md)

## Sprint 1 — Core Foundation

### Objectif

Construire le cœur indépendant de Communication Center avant toute intégration
avec une application CakePHP.

### Terminé

- [x] Configuration de l'environnement de développement
- [x] Autoload PSR-4
- [x] PHP CodeSniffer avec standard CakePHP
- [x] PHPUnit
- [x] Classe principale `CommunicationCenterPlugin`
- [x] Objet `Recipient`
- [x] Contrat `RecipientProviderInterface`
- [x] Objet `ChannelAction`
- [x] Contrat `ChannelInterface`
- [x] Contrat `MessageRendererInterface`
- [x] `SimpleMessageRenderer`
- [x] Support des variables `{{variable}}`
- [x] Objet `PhoneNumber`
- [x] Validation des numéros internationaux
- [x] Support du format international E.164
- [x] `WhatsAppChannel`
- [x] Génération des liens `wa.me`
- [x] `CommunicationService`
- [x] Personnalisation d'un message par destinataire
- [x] Exclusion automatique des destinataires incompatibles avec un canal
- [x] Support de destinataires de différents pays
- [x] Suite de tests automatisés

### Architecture obtenue

Application hôte
    ↓
RecipientProviderInterface
    ↓
Recipient
    ↓
MessageRendererInterface
    ↓
CommunicationService
    ↓
ChannelInterface
    ↓
ChannelAction

## Sprint 2 — CakePHP Integration & Mobile UI

### Objectif

Permettre à une application CakePHP d'utiliser Communication Center et proposer
une interface responsive permettant de sélectionner des destinataires, personnaliser
un message et préparer leur traitement via différents canaux.

### Terminé

- [x] Intégration dans une application CakePHP 5.4
- [x] Enregistrement des services dans le container CakePHP
- [x] `ChannelRegistry`
- [x] `RecipientProviderRegistry`
- [x] Enregistrement de Providers depuis l'application hôte
- [x] Canal WhatsApp fourni nativement
- [x] Routes du plugin
- [x] `CommunicationCentersController`
- [x] Interface Bootstrap responsive
- [x] Sélection d'un Recipient Provider
- [x] Chargement dynamique des destinataires
- [x] Liste des destinataires
- [x] Cases à cocher
- [x] Sélection multiple
- [x] Tout sélectionner / Tout désélectionner
- [x] Compteur dynamique de sélection
- [x] Validation des numéros WhatsApp
- [x] Désactivation des destinataires incompatibles
- [x] Zone de rédaction du message
- [x] Variables de personnalisation
- [x] Personnalisation du message par destinataire
- [x] Préparation des liens WhatsApp
- [x] `ChannelAction` liée au destinataire
- [x] Conservation du message rendu dans `ChannelAction`
- [x] Écran des communications préparées
- [x] Ouverture individuelle dans WhatsApp
- [x] Marquage manuel comme traité
- [x] Compteur de progression
- [x] Barre de progression
- [x] Navigation vers le prochain destinataire
- [x] Conservation temporaire de la progression dans le navigateur
- [x] Tests automatisés du moteur
- [x] Validation dans une véritable application CakePHP sandbox

### Architecture obtenue

Application CakePHP
        │
        ├── Recipient Providers métier
        │
        ▼
RecipientProviderRegistry
        │
        ▼
Communication Center
        │
        ├── MessageRenderer
        ├── CommunicationService
        └── ChannelRegistry
                │
                ▼
           WhatsAppChannel
                │
                ▼
           ChannelAction[]
                │
                ▼
        Interface de traitement

## V1 completed

- [x] WhatsApp channel
- [x] Email channel
- [x] international phone numbers
- [x] recipient providers
- [x] filterable recipient providers
- [x] host-defined business filters
- [x] personalized variables
- [x] persistent campaigns
- [x] recipient snapshots
- [x] campaign progress
- [x] campaign history
- [x] archive and restore
- [x] dashboard statistics
- [x] reusable message templates
- [x] channel-specific templates
- [x] template activation/deactivation
- [x] template pre-fill during campaign creation
- [x] responsive Bootstrap interface
- [x] CakePHP service container integration
- [x] automated tests
- [x] CakePHP coding-standard validation
- [x] validation in a separate CakePHP sandbox

## V1 scope

V1 deliberately stays focused.

Advanced features such as campaign/template analytics, persistent
`communication_template_id` relationships, campaign duplication and advanced
automation are intentionally left for a later version.

The priority of V1 is a stable and reusable integration boundary between
Communication Center and CakePHP host applications.
