#!/usr/bin/env bash

OPERATION=$1
shift
ARGS=$*

case $OPERATION in
  'config-export') ddev exec drupal config:export $ARGS;;

  'drupal-install')
    ddev composer install
    ddev exec drush si config_installer -y --account-name=admin --account-pass=admin123
    ddev exec drupal cache:rebuild
    ;;

  'drupal-refresh')
    ddev composer install
    ddev exec drupal cache:rebuild
    ddev exec drupal config:import
    ddev exec drupal update:execute
    ddev exec drupal cache:rebuild
    ;;

  # Defer anything else to ddev.
  *) ddev $ARGS;;
esac
