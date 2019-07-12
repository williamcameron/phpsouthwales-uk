#!/usr/bin/env bash

OPERATION=$1
shift
ARGS=$*

DOCROOT="web"
THEME_DIR="${DOCROOT}/themes/custom/phpsouthwales"

case ${OPERATION} in
  'drupal-install')
    fin composer install
    fin drush site:install config_installer -y --account-name=admin --account-pass=admin123
    fin drush features:import:all -y
    fin drush migrate:import --all
    ./helper.sh build-theme
    fin exec drupal cache:rebuild
    fin uli
    ;;

  'drupal-refresh')
    fin composer install
    fin exec drupal cache:rebuild
    fin drush features:import:all -y
    fin exec drupal config:import
    fin exec drupal update:execute
    ./helper.sh build-theme
    fin exec drupal cache:rebuild
    ;;

  'build-theme')
    pushd ${THEME_DIR}
    fin exec yarn
    fin exec yarn build
    popd
    ;;
esac
