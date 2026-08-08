# CakePHP Communication Center

A reusable, mobile-first communication hub for CakePHP 5 applications.

## Vision

The plugin centralizes campaigns, recipients, templates, channels and delivery history without depending on any application's business model.

The plugin does not know what a volunteer, member, customer or citizen is. It only works with recipients exposed by a recipient provider.

## Initial use case

The first integration will be host application:

- identify members whose monthly contribution has not been recorded;
- select recipients from a mobile interface;
- prepare individualized WhatsApp messages;
- process recipients one by one;
- preserve campaign progress and delivery history.

## Principles

- CakePHP 5 compatible
- PHP 8.1+
- mobile first
- Bootstrap 5
- no mandatory jQuery dependency
- business-agnostic core
- extensible channels and recipient providers
- English code, translatable user interface
- tests and documentation from the beginning

## Planned installation

```bash
composer require vendor/communication-center
bin/cake plugin load CommunicationCenter
bin/cake migrations migrate --plugin CommunicationCenter
```

The package is currently in architectural design and is not yet installable.

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
