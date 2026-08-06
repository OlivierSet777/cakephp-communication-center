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
