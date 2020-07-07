# PHP South Wales Drupal codebase

The Drupal codebase for the [PHP South Wales user group](https://www.phpsouthwales.uk) website.

## Local environment

The project is using Docker and Docker Compose for local development.

Run the `make` command to create the required files, download Drupal core, the additional contrib modules and PHP libraries, install Drupal, and generate the theme assets.

If you are using [Traefik](https://docs.traefik.io) (recommended), then the site should now be available at <http://phpsouthwales.docker.localhost>.

## Updating Drupal core using Composer

This can be done using the `bin/composer.sh` helper:

    bin/composer.sh update 'drupal/core-*'

## Hosting

The hosting is provided and sponsored by [Platform.sh](http://platform.sh/?medium=referral&utm_campaign=sponsored_sites&utm_source=phpsouthwales).
