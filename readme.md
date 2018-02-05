# Internationalized routing for Symfony 4

> This bundle provides i18n routing for Symfony 4.

[![Author](https://img.shields.io/badge/author-@frankdejonge-blue.svg?style=flat-square)](https://twitter.com/frankdejonge)
[![Build Status](https://img.shields.io/travis/frankdejonge/symfony-i18n-routing/master.svg?style=flat-square)](https://travis-ci.org/frankdejonge/symfony-i18n-routing)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/frankdejonge/symfony-i18n-routing.svg?style=flat-square)](https://scrutinizer-ci.com/g/frankdejonge/symfony-i18n-routing/code-structure)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Packagist Version](https://img.shields.io/packagist/v/frankdejonge/symfony-i18n-routing.svg?style=flat-square)](https://packagist.org/packages/frankdejonge/symfony-i18n-routing)
[![Total Downloads](https://img.shields.io/packagist/dt/frankdejonge/symfony-i18n-routing.svg?style=flat-square)](https://packagist.org/packages/frankdejonge/symfony-i18n-routing)

## Purpose

This bundle provides a method of internationalization of route definitions. This means 
you can define a path per locale and still have them route to the same controller action.

## Usage

```bash
composer req frankdejonge/symfony-i18n-routing
```

Register the bundle in `bundles.php`

```php
<?php

return [
    FrankDeJonge\SymfonyI18nRouting\I18nRoutingBundle::class => ['all' => true],
    // ...
];
```

## Yaml usage

From your main `config/routes.yml` import your localized routes:

```yaml
i18n_routes:
    resource: ./i18n_routes/routes.yml
    type: i18n_routes
```

Now you can define i18n routes in `config/i18n_routes/routes.yml`:

```yaml
contact:
    controller: ContactController::formAction
    locales:
        en: /send-us-an-email
        nl: /stuur-ons-een-email
```

This is effectively the same as defining:

```yaml
contact.en:
    controller: ContactController::formAction
    path: /send-us-an-email
    defaults:
        _locale: en

contact.nl:
    controller: ContactController::formAction
    path: /stuur-ons-een-email
    defaults:
        _locale: nl
```

As you can see this saves you a bit of typing and prevents you from
having to keep 2 definitions in sync (less error prone).