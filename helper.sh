#!/usr/bin/env bash

OPERATION=$1
shift
ARGS=$*

PROJECT_ROOT="/var/www/html"
DOCROOT="web"
THEME_DIR="${PROJECT_ROOT}/${DOCROOT}/themes/custom/phpsouthwales"

case $OPERATION in
  'drupal-install')
    fin composer install
    fin drush site:install config_installer -y --account-name=admin --account-pass=admin123
    fin drush migrate:import --all
    ./helper.sh build-theme
    fin exec drupal cache:rebuild
    fin uli
    ;;

  'drupal-refresh')
    fin composer install
    fin exec drupal cache:rebuild
    fin exec drupal config:import
    fin exec drupal update:execute
    ./helper.sh build-theme
    fin exec drupal cache:rebuild
    ;;

  'build-theme')
    # fin --dir $THEME_DIR yarn
    # fin --dir $THEME_DIR yarn build
    ;;
esac
