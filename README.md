# PHP South Wales Drupal codebase

The Drupal codebase for the [PHP South Wales user group](https://www.phpsouthwales.uk) website.

## Local environment

The project is using DDEV for local development.

### Installing the site

To install the site from scratch, run `ddev drupal-site-install`. This will install Drupal from the existing configuration, build the theme assets, and import a set of events from Meetup.com.

## Updating Drupal core using Composer

    ddev composer update 'drupal/core-*'

## Hosting

The hosting is provided and sponsored by [Platform.sh](http://platform.sh/?medium=referral&utm_campaign=sponsored_sites&utm_source=phpsouthwales).
