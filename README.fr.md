# CakePHP Communication Center

🇬🇧 English documentation: [README.md](README.md)

Un centre de communication réutilisable et pensé pour le mobile, destiné aux applications CakePHP 5.

## Vision

Communication Center centralise les campagnes de communication, les destinataires,
les modèles de messages réutilisables, les canaux et l'historique de traitement,
sans dépendre du modèle métier de l'application hôte.

Le plugin n'a pas besoin de savoir ce qu'est un adhérent, un bénévole, un client
ou un citoyen. L'application hôte expose simplement ses destinataires via un
Recipient Provider, puis Communication Center prend en charge le workflow de
communication.

## État actuel

La V1 de Communication Center est fonctionnelle et a été validée dans une
application CakePHP sandbox séparée.

Fonctionnalités actuellement prises en charge :

- campagnes WhatsApp ;
- campagnes Email ;
- campagnes persistantes et instantanés des destinataires ;
- modèles de messages réutilisables ;
- filtres de destinataires fournis par l'application hôte ;
- variables de personnalisation ;
- suivi de progression des campagnes ;
- historique des campagnes ;
- archivage et restauration ;
- interface responsive Bootstrap 5 ;
- tests automatisés et contrôle du standard de code CakePHP.

## Exemple de cas d'utilisation

host application est la première intégration réelle prévue.

Un workflow typique peut être :

1. exposer les adhérents via un Recipient Provider ;
2. filtrer les adhérents dont la cotisation mensuelle n'a pas été enregistrée ;
3. sélectionner les destinataires ;
4. choisir WhatsApp ou Email ;
5. sélectionner éventuellement un modèle de message ;
6. préparer les messages personnalisés ;
7. traiter ou envoyer les messages ;
8. conserver la progression et l'historique de la campagne.

host application n'est qu'un exemple d'intégration. Le plugin reste indépendant de tout
métier particulier.

## Prérequis

- CakePHP 5
- PHP 8.1+
- Bootstrap 5 CSS pour l'interface fournie
- Composer

## Principes

- compatible CakePHP 5 ;
- PHP 8.1+ ;
- mobile first ;
- Bootstrap 5 CSS requis par l'interface V1 ;
- CakePHP Bootstrap-UI n'est pas requis ;
- aucune dépendance obligatoire à jQuery ;
- cœur indépendant du métier ;
- canaux et Recipient Providers extensibles ;
- les données métier restent sous la responsabilité de l'application hôte ;
- code source en anglais ;
- interface utilisateur traduisible ;
- tests et documentation.

## Dépendance de l'interface V1

L'interface fournie par Communication Center utilise les classes CSS de Bootstrap 5.
L'application hôte doit donc charger **Bootstrap 5 CSS** pour obtenir la mise en page prévue.

Le plugin CakePHP **Bootstrap-UI n'est pas nécessaire**. Communication Center n'utilise pas ses helpers : seule la feuille de style Bootstrap 5 est requise.

Sans Bootstrap 5 CSS, les fonctionnalités du plugin restent disponibles, mais l'interface ne bénéficie pas de la mise en page et du rendu prévus.

Une future version pourra rendre l'interface totalement autonome afin de supprimer cette dépendance visuelle.

## Installation

Chargez le plugin dans l'application CakePHP hôte :

```bash
bin/cake plugin load CommunicationCenter
```

Exécutez les migrations du plugin :

```bash
bin/cake migrations migrate -p CommunicationCenter
```

Vérifiez leur état avec :

```bash
bin/cake migrations status -p CommunicationCenter
```

Les routes du plugin sont disponibles sous :

```text
/communication-center
```

## Intégration dans l'application hôte

Communication Center reste indépendant du modèle métier de l'application hôte.

Le plugin fournit notamment :

- `ChannelRegistry` ;
- `RecipientProviderRegistry` ;
- `CommunicationService` ;
- `CampaignService` ;
- `EmailCampaignService` ;
- `WhatsAppChannel` ;
- `EmailChannel` ;
- la persistance des campagnes et des destinataires ;
- les modèles de messages ;
- l'interface Communication Center.

L'application hôte fournit :

- les données des destinataires ;
- les filtres métier ;
- la configuration SMTP ;
- un ou plusieurs Recipient Providers.

Le plugin ne doit donc pas connaître directement les tables `Users`, `Members`,
`Volunteers` ou les autres entités métier de l'application.

## Recipient Providers

Les Recipient Providers constituent la frontière entre Communication Center et
l'application hôte.

Une application implémente :

```php
CommunicationCenter\Recipient\Provider\RecipientProviderInterface
```

ou, lorsqu'elle souhaite proposer des filtres :

```php
CommunicationCenter\Recipient\Provider\FilterableRecipientProviderInterface
```

### Exemple minimal

```php
<?php
declare(strict_types=1);

namespace App\CommunicationCenter;

use CommunicationCenter\Recipient\Provider\RecipientProviderInterface;
use CommunicationCenter\Recipient\Recipient;

final class AppRecipientProvider implements RecipientProviderInterface
{
    public function getRecipients(array $criteria = []): iterable
    {
        yield new Recipient(
            externalId: '1',
            firstname: 'Jean',
            lastname: 'Dupont',
            phone: '+33612345678',
            email: 'jean.dupont@example.com',
        );
    }
}
```

`externalId` correspond à l'identifiant utilisé par l'application hôte pour
identifier le destinataire.

## Enregistrement d'un Recipient Provider

L'application hôte enregistre ses providers dans `Application::services()` via
`RecipientProviderRegistry`.

Exemple :

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

Le sandbox utilise exactement ce mécanisme avec `DemoRecipientProvider`.

Le registre appartient au plugin, tandis que les providers contenant la logique
métier appartiennent à l'application hôte.

## Filtres métier

Une application peut exposer ses propres filtres en implémentant
`FilterableRecipientProviderInterface`.

Exemple :

```php
public function getFilters(): array
{
    return [
        'payment_status' => [
            'label' => 'Cotisation',
            'options' => [
                'unpaid' => 'Non payée',
                'paid' => 'Payée',
            ],
        ],
        'recyclery' => [
            'label' => 'Recyclerie',
            'options' => [
                'cahors' => 'Cahors',
                'negrepelisse' => 'Nègrepelisse',
            ],
        ],
    ];
}
```

L'application hôte reste responsable de l'application de ces critères dans
`getRecipients()`.

Ainsi, les filtres restent spécifiques au métier sans rendre le plugin dépendant
d'host application ou d'une autre application.

## Objet Recipient

Communication Center travaille avec son propre objet `Recipient`.

Un destinataire peut fournir :

- un identifiant externe ;
- un prénom ;
- un nom ;
- un numéro de téléphone ;
- une adresse Email ;
- des variables personnalisées.

Exemple :

```php
yield new Recipient(
    externalId: '42',
    firstname: 'Sophie',
    lastname: 'Martin',
    phone: '+33611223344',
    email: 'sophie@example.com',
    variables: [
        'month' => 'Août',
    ],
);
```

## Variables dans les messages

Les messages peuvent utiliser :

```text
{{firstname}}
{{lastname}}
```

Le provider peut également fournir des variables personnalisées :

```text
{{month}}
```

Exemple :

```text
Bonjour {{firstname}},

Votre cotisation du mois de {{month}} n'a pas encore été enregistrée.
```

`SimpleMessageRenderer` génère le message final individuellement pour chaque
destinataire.

## Canaux

Les canaux sont enregistrés dans `ChannelRegistry`.

La V1 fournit actuellement :

- WhatsApp ;
- Email.

L'architecture permet d'ajouter ultérieurement d'autres canaux sans les coupler
aux Recipient Providers.

## WhatsApp

`WhatsAppChannel` prépare les actions WhatsApp pour les destinataires disposant
d'un numéro compatible.

Le plugin prend en charge les numéros internationaux au format E.164.

Le workflow actuel prépare les liens permettant à l'opérateur d'ouvrir la
communication personnalisée et de traiter les destinataires individuellement.

## Email

Les campagnes Email utilisent `EmailCampaignService` et `CakeEmailSender`.

Elles prennent notamment en charge :

- la compatibilité Email des destinataires ;
- l'objet du message ;
- le contenu personnalisé ;
- l'envoi individuel ;
- l'envoi de tous les emails en attente ;
- le suivi de traitement de la campagne.

### Configuration SMTP

La configuration SMTP appartient à l'application CakePHP hôte.

Communication Center ne stocke pas les identifiants SMTP.

Configurez normalement le transport et le profil Email de CakePHP dans
l'application hôte. `CakeEmailSender` utilise ensuite cette infrastructure.

## Modèles de messages

La V1 inclut des modèles de messages réutilisables.

Un modèle contient :

- un nom ;
- un canal ;
- un objet optionnel pour Email ;
- le corps du message ;
- un statut actif/inactif.

Seuls les modèles actifs correspondant au canal de la campagne sont proposés.

Lorsqu'un modèle est sélectionné, l'objet et le message sont préremplis mais
restent modifiables avant la préparation de la campagne.

Gestion des modèles :

```text
/communication-center/templates
```

La V1 ne conserve volontairement pas de relation persistante entre une campagne
et le modèle ayant servi à la créer. Le modèle sert actuellement uniquement à
préremplir le contenu.

## Workflow d'une campagne

```text
Application hôte
        |
        v
RecipientProviderInterface
        |
        v
RecipientProviderRegistry
        |
        v
Recipient[]
        |
        v
Interface Communication Center
        |
        +--> ChannelRegistry
        |
        +--> Modèle de message (optionnel)
        |
        v
CommunicationService
        |
        v
ChannelAction[] personnalisées
        |
        v
Campagne persistante + instantanés destinataires
        |
        v
Traitement / Envoi
        |
        v
Progression et historique
```

## Historique des campagnes

Les campagnes sont persistées et peuvent être rouvertes ultérieurement.

La V1 conserve notamment :

- le nom de la campagne ;
- le provider ;
- le canal ;
- l'objet Email éventuel ;
- le contenu original du message ;
- le nombre de destinataires ;
- le nombre de destinataires traités ;
- le statut ;
- les instantanés des destinataires ;
- le message rendu pour chaque destinataire.

Les campagnes peuvent également être archivées puis restaurées.

## Interface utilisateur

L'interface fournie est responsive et pensée pour ordinateur et mobile.

Elle comprend actuellement :

- statistiques du tableau de bord ;
- choix du canal ;
- choix du Recipient Provider ;
- filtres métier dynamiques ;
- liste des destinataires ;
- état de compatibilité ;
- sélection multiple ;
- tout sélectionner / tout désélectionner ;
- compteur des destinataires sélectionnés ;
- sélection d'un modèle ;
- objet Email ;
- éditeur du message ;
- variables personnalisées ;
- préparation de la campagne ;
- historique ;
- suivi de progression ;
- archivage et restauration ;
- gestion des modèles.

## Routes principales

Les routes sont regroupées sous :

```text
/communication-center
```

Principaux écrans :

```text
/communication-center
/communication-center/campaigns
/communication-center/archives
/communication-center/templates
/communication-center/templates/add
```

Les routes de détail, de traitement, de modification et d'activation des modèles
sont gérées par la configuration des routes du plugin.

## Sandbox

Le développement est validé avec une application CakePHP sandbox séparée.

Cette application démontre la séparation recherchée :

```text
Plugin Communication Center
        |
        +-- canaux
        +-- services
        +-- campagnes
        +-- modèles
        +-- interface
        |
        v
RecipientProviderRegistry
        ^
        |
Application hôte
        |
        +-- DemoRecipientProvider
        +-- filtres métier
        +-- données destinataires
```

`DemoRecipientProvider` reste volontairement en dehors du plugin.

Une application de production le remplace par son propre provider.

## Développement

Installer les dépendances :

```bash
composer install
```

Vérifier le standard de code CakePHP :

```bash
composer cs-check
```

Corriger automatiquement ce qui peut l'être :

```bash
composer cs-fix
```

Exécuter les tests :

```bash
composer test
```

Avant un commit, la base attendue est :

```bash
composer cs-check
composer test
```

Les deux commandes doivent être vertes.

## Architecture

Le cœur reste organisé ainsi :

```text
Application hôte
        |
        v
RecipientProviderInterface
        |
        v
Recipient
        |
        v
MessageRendererInterface
        |
        v
CommunicationService
        |
        v
ChannelInterface
        |
        v
ChannelAction
```

L'intégration CakePHP ajoute autour de ce cœur les registres, la persistance et
l'interface utilisateur.

## Sprint 1 — Fondations du cœur

### Objectif

Construire le cœur indépendant de Communication Center avant toute intégration
dans une application CakePHP.

### Terminé

- [x] environnement de développement ;
- [x] autoload PSR-4 ;
- [x] PHP CodeSniffer avec standard CakePHP ;
- [x] PHPUnit ;
- [x] `CommunicationCenterPlugin` ;
- [x] `Recipient` ;
- [x] `RecipientProviderInterface` ;
- [x] `ChannelAction` ;
- [x] `ChannelInterface` ;
- [x] `MessageRendererInterface` ;
- [x] `SimpleMessageRenderer` ;
- [x] variables `{{variable}}` ;
- [x] `PhoneNumber` ;
- [x] validation des numéros internationaux ;
- [x] format E.164 ;
- [x] `WhatsAppChannel` ;
- [x] génération des liens WhatsApp ;
- [x] `CommunicationService` ;
- [x] personnalisation par destinataire ;
- [x] exclusion des destinataires incompatibles ;
- [x] destinataires internationaux ;
- [x] tests automatisés.

## Sprint 2 — Intégration CakePHP et interface mobile

### Objectif

Intégrer le cœur dans CakePHP et proposer une interface responsive permettant de
sélectionner les destinataires, personnaliser les messages et traiter les
communications.

### Terminé

- [x] intégration dans le sandbox CakePHP ;
- [x] services enregistrés dans le conteneur CakePHP ;
- [x] `ChannelRegistry` ;
- [x] `RecipientProviderRegistry` ;
- [x] enregistrement des providers depuis l'application hôte ;
- [x] routes du plugin ;
- [x] interface Bootstrap responsive ;
- [x] sélection du provider ;
- [x] chargement dynamique des destinataires ;
- [x] sélection des destinataires ;
- [x] tout sélectionner / tout désélectionner ;
- [x] compteur dynamique ;
- [x] vérification de compatibilité avec les canaux ;
- [x] éditeur de message ;
- [x] variables de personnalisation ;
- [x] actions de communication préparées ;
- [x] interface de traitement ;
- [x] suivi de progression ;
- [x] tests automatisés ;
- [x] validation dans une véritable application CakePHP sandbox.

## Fonctionnalités ajoutées à la V1

- [x] campagnes persistantes ;
- [x] instantanés persistants des destinataires ;
- [x] historique des campagnes ;
- [x] archivage et restauration ;
- [x] statistiques du tableau de bord ;
- [x] Recipient Providers filtrables ;
- [x] filtres métier définis par l'application hôte ;
- [x] canal Email ;
- [x] objets Email ;
- [x] envoi individuel des emails ;
- [x] envoi groupé des emails en attente ;
- [x] modèles de messages réutilisables ;
- [x] modèles spécifiques aux canaux ;
- [x] activation/désactivation des modèles ;
- [x] préremplissage automatique d'une campagne depuis un modèle.

## Périmètre de la V1

La V1 reste volontairement ciblée.

Les fonctions avancées telles que les statistiques par modèle, la relation
persistante `communication_template_id`, la duplication des campagnes ou les
automatisations avancées sont volontairement réservées à une version ultérieure.

La priorité de la V1 est de fournir une frontière d'intégration stable et
réutilisable entre Communication Center et les applications CakePHP hôtes.

## Documentation complémentaire

- [Vision](docs/01-Vision.md)
- [Périmètre](docs/02-Scope.md)
- [Architecture](docs/03-Architecture.md)
- [Base de données](docs/04-Database.md)
- [Recipient Providers](docs/05-Recipient-Providers.md)
- [Canaux](docs/06-Channels.md)
- [Interface utilisateur](docs/07-User-Interface.md)
- [Roadmap](ROADMAP.md)
