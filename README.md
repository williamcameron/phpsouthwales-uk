# PHP South Wales Drupal codebase

The Drupal codebase for the [PHP South Wales user group](https://www.phpsouthwales.uk) website.

## Local environment

The project is using the [Symfony web server](https://symfony.com/doc/current/setup/symfony_server.html) for local development.

First, install the PHP dependencies using Composer:

    symfony composer install

This will download Drupal core, the additional contrib modules and PHP libraries.

## Updating Drupal core using Composer

    symfony composer update 'drupal/core-*'

## Hosting

The hosting is provided and sponsored by [Platform.sh](http://platform.sh/?medium=referral&utm_campaign=sponsored_sites&utm_source=phpsouthwales).
