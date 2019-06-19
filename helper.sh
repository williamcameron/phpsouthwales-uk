#!/usr/bin/env bash

OPERATION=$1
shift
ARGS=$*

case $OPERATION in
  'config-export') ddev exec drupal config:export $ARGS;;
  'drupal-install')
    ddev exec drush site:install -y
    ddev exec drush config:set -y system.site uuid 002d5245-82cf-4586-b57f-5ab64ebcd877
    ddev exec drush config:delete -y shortcut.set.default
    ddev exec drush config:import -y --source=../config/sync
    ;;
  'drupal-refresh')
    ddev composer install
    ddev exec drupal cache:rebuild
    ddev exec drupal config:import
    ddev exec drupal update:execute
    ddev exec drupal cache:rebuild
    ;;
esac
