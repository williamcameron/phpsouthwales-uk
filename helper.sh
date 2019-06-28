#!/usr/bin/env bash

OPERATION=$1
shift
ARGS=$*

THEME_DIR="/var/www/html/web/themes/custom/phpsouthwales"

case $OPERATION in
  'config-export') ddev exec drupal config:export $ARGS;;

  'drupal-install')
    ddev composer install
    ddev exec drush si config_installer -y --account-name=admin --account-pass=admin123
    ./helper.sh build-theme
    ddev exec drupal cache:rebuild
    ;;

  'drupal-refresh')
    ddev composer install
    ddev exec drupal cache:rebuild
    ddev exec drupal config:import
    ddev exec drupal update:execute
    ./helper.sh build-theme
    ddev exec drupal cache:rebuild
    ;;

  'build-theme')
    ddev exec --dir $THEME_DIR yarn
    ddev exec --dir $THEME_DIR yarn build
    ;;

  # Defer anything else to ddev.
  *) ddev $ARGS;;
esac
